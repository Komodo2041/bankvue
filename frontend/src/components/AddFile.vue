<template>
  <div id="fileAdd">
    <h1>{{ msg }}</h1>
    <form action="" method="POST" v-on:submit="handle" enctype="multipart/form-data">   
        <input type="file" class="form-control" id="fileimport" name="fileimport" accept=".json,.csv,.xml" />
        <input type="submit" class="btn btn-primary mt-2" value="Dodaj import" />
     </form>
      <p v-if="message" class="mt-4 text-green-600">{{ message }}</p>
      <p v-if="error" class="mt-4 text-red-600">{{ error }}</p>    
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
       file: []
    }
  },      
  props: {
    msg: String
  },
  methods: {
    handle : function() {
      event.preventDefault();
      const file = document.getElementById("fileimport").files[0];
  
      if (file) {
        this.file = file
        this.message="Przesyłanie pliku ...";
        this.error = ""

        this.addFile({file:this.file}).then(response => {
            const res = response;
  

            if (res.errors.file) {
               this.error = res.errors.file.join()
               this.message = "";
            } else if (res.exception) {
                this.error = res.error;
            } else {

              this.message = "";
              this.$emit('file-added');
            }

        }).catch(error => { 
           console.log(error);
        });

      } else {
        this.error="Brak pliku";
      }

      return false;
    },
    clear: function() {
       this.message = "";
       this.error = "";
    },
    addFile : async function(data) {
        let link = "http://127.0.0.1:8000/api/imports";
        try {
            const response = await axios.post(link, data, { headers: { 'Content-Type': 'multipart/form-data' }}
          );
            return response.data;
       } catch (error) {  
          return error.response.data;  
       }
    }    
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
#fileAdd {
   background: #c8dba8;
   padding:20px;
}
 #fileimport {
  width:300px;
  margin:auto;
 }
</style>
