<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transactions;
use App\Models\Imports;
use App\Models\ImportsLogs;

class ImportController extends Controller
{
    
    
   public function addFile(Request $request) {

        $request->validate([ 
            'file'  => 'required|file|max:1024|mimes:csv,json,xml,txt',  
        ]);
      
        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());
        $extension = $file->getClientOriginalExtension();
  
 
        if ($extension === 'json') {
            $importData = json_decode($content, true); 
        }
        if ($extension === 'csv') {
            $csvData = $this->readCSVData($content); 
            if ($csvData['success']) {
               $importData = $csvData['data'];
            } else {
               return response()->json(['message' => 'CSV ERROR', 'errors' => ['file' => $csvData['errors']]], 400);
            }
        }
        if ($extension === 'xml') {
            libxml_use_internal_errors(true);  
            $xml = simplexml_load_file($file->getRealPath());
 
            if ($xml === false) {
               $errors = libxml_get_errors();
               return response()->json(['message' => 'XML ERROR', 'errors' => ['file' => $errors]], 400);
                
            } else {
               $importData = json_decode(json_encode($xml), true);
               if (isset($importData['transaction'])) {
                  $importData = $importData['transaction'];
               } else {
                  return response()->json(['message' => 'XML ERROR no transaction', 'errors' => ['file' => 'No data to import']], 400);
               }
            }
        } 

        if (empty($importData)) {
            return response()->json(['message' => 'No Data', 'errors' => ['file' => 'No data to import']], 400);
        }

        $this->importFile($importData, $file->getClientOriginalName());
        return response()->json(['message' => "Import Success", 'con' => $importData, 'errors' => ''], 200);
 
   }

   private function readCSVData($data) {
      $res = []; 
      $lines = explode("\n", $data);
      $header = explode(",", trim($lines[0]));
      if ($errors = $this->checkHeader($header)) {
           return ['success' => false, 'errors' => $errors];
      }
      for ($i = 1; $i < count($lines); $i++) {
        $record = [];
        if (!trim($lines[$i])) {
            continue;
        }
        $line = explode(",", trim($lines[$i]));
        foreach ($line AS $key => $value) {
            $record[$header[$key]] = $value;
        }
        $res[] = $record;
      }
      return ['success' => true, 'data' => $res];
   }

   private function checkHeader($header) {
      $errors = [];
      $check = ['transaction_id','account_number','transaction_date','amount','currency' ];
      foreach ($check AS $val) {
        if (!in_array($val, $header)) {
            $errors[] = "none Field ".$val;
        }
      }
      return $errors;
   }

   private function importFile($data, $fileName) {
    
      $badImport = 0;
      $goodImport = 0;

      $Import = Imports::create([
         'file_name' => $fileName,
         'total_records' => count($data)
      ]);

      foreach ($data AS $record) {
         $errors = $this->checkRecordImport($record);
         if (!isset($record['transaction_id'])) {
            $record['transaction_id'] = "-";
         }
         if ($errors) {
            ImportsLogs::create([
               "import_id" =>  $Import->id,
               "transaction_id" => $record['transaction_id'],
               "error_message" => implode(",", $errors)
            ]);
            $badImport++; 
         } else {
            Transactions::create([
               "import_id" =>  $Import->id,
               "transaction_id" => $record['transaction_id'],
               "account_number" => $record['account_number'],
               "transaction_date" => $record['transaction_date'],
               "amount" => $record['amount'],
               "currency" => $record['currency']
            ]);
            $goodImport++; 
         } 
      }
      $Import->failed_records = $badImport;
      $Import->successful_records = $goodImport;
      if ($goodImport > 0) {
        if ($badImport > 0) {
            $Import->status = "partial";
        } else {
            $Import->status = "success";
        }
      } else {
          $Import->status = "failed";
      }
      $Import->save();
   }
 

   private function checkRecordImport($record) {
      $errors = [];
      $check = ['transaction_id','account_number','transaction_date','amount','currency' ];
      foreach ($check AS $val) {
        if (!isset($record[$val]) || !$record[$val]) {
            $errors[] = "none Field ".$val;
        }
      }

      if ($errors) {
        return $errors;
      }

      if (!in_array($record['currency'], ['USD', 'PLN'])) {
        $errors[] = "Bad Currency ";
      }

      if ((int)$record['amount'] <= 0) {
         $errors[] = "Bad Amount ";
      }

      if ($this->isValidIBAN($record['account_number'])) {
        $errors[] = "Bad account_number";
      }

      if ($this->isDuplicateTransaction($record['transaction_id'])) {
          $errors[] = 'Transakcja już istnieje';
      }

      return $errors;
   }

    private function isDuplicateTransaction(string $id)
    {
        return Transactions::where('transaction_id', $id)->exists();
    }   

    private function isValidIBAN(string $iban)
    {
        $iban = preg_replace('/[^A-Z0-9]/', '', strtoupper($iban));
        if (strlen($iban) < 15 || !preg_match('/^[A-Z]{2}/', $iban)) return false;

        $moved = substr($iban, 4) . substr($iban, 0, 4);
        $numeric = '';
        foreach (str_split($moved) as $char) {
            $numeric .= is_numeric($char) ? $char : (ord($char) - 55);
        }

        return bcmod($numeric, '97') === '1';
    }

    public function agetImports() {
        $imports = Imports::all();
        return response()->json(['message' => "Success", 'imports' => $imports, 'errors' => ''], 200);
    }

    public function importRecord($id) {
        $import = Imports::with(['transactions', 'importlogs'])->find($id);
       
        if ($import) {
             return response()->json(['message' => "Success", 'import' => $import, 
             'transactions' => $import->transactions, 'importslogs' => $import->importlogs, 'errors' => ''], 200);             
        } 
        return response()->json(['message' => "Error", 'errors' => 'No Found Import'], 400);
    }

}
