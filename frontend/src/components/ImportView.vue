<template>
   <div>
       <div v-if="importData">
            <p><b>Id</b> : {{importData.id}}, <b>File</b> : {{importData.file_name}}</p>
            <h3>Poprawne Transakcje</h3>
            <table class="table table-striped table-bordered table-hover"  v-show="transactions.length" >
                <tr>
                <th>Id transakcji</th>
                <th>Account Number</th>
                <th>Amount</th>
                <th>Waluta</th>
                <th>Date</th>
                </tr>
                <tr v-for="t in transactions" v-bind:key="t.id" >
                <td>{{t.transaction_id}}</td>
                <td>{{t.account_number}}</td>
                <td>{{t.amount}}</td>
                <td>{{t.currency}}</td>
                <td>{{t.created_at}}</td>   
     
                </tr>
            </table>
            <div v-show="transactions.length == 0">
                <p>Brak poprawnych transakcji</p>
            </div> 
            <br/><br/>

            <h3>Niuedane Transakcje</h3>
            <table class="table table-striped table-bordered table-hover"  v-show="logs.length" >
                <tr>
                  <th>Id transakcji</th>
                   <th>Error</th>
                   <th>Date</th>
                </tr>
                <tr v-for="l in logs" v-bind:key="l.id" >
                    <td>{{l.transaction_id}}</td>
                    <td>{{l.error_message}}</td>
                    <td>{{l.created_at}}</td>        
                </tr>
            </table>
            <div v-show="logs.length == 0">
                <p>Brak niuedanych transakcji</p>
            </div> 

       </div>
       <div v-else>
         <h4>Nie znaleziono importu</h4>
       </div>
    
   </div>
</template>

<script>


  import axios from 'axios'
 

export default {
  name: 'AddFile',
  data() {
    return {
       error:"",
       message:"",
       importData: [],
       transactions: [],
       logs: []
    }
  },      
  props: {
     id: {
        type: [Number, String],
        required: true
     }
  },
  methods: {
    getImport : async function() {
        let link = "http://127.0.0.1:8000/api/imports/" + this.id;
        try {
            const response = await axios.get(link);
            return response.data;
       } catch (error) {  
          return error.response.data;  
       }
    },  
  },
    async created() {
    const data = await this.getImport() 
    console.log(data);
      this.importData = data.import;
      this.transactions = data.transactions;
      this.logs = data.importslogs;

    }    
}
</script>
 <style>
 </style>