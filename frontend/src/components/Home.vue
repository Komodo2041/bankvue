<template>
  <div>
 
     <table class="table table-striped table-bordered table-hover"  v-show="imports.length" >
        <tr>
           <th>File</th>
           <th>Rekordy</th>
           <th>Status</th>
           <th>Date</th>
           <th></th>
        </tr>
        <tr v-for="i in imports" v-bind:key="i.id" >
           <td>{{i.file_name}}</td>
           <td>{{i.total_records}}</td>
           <td>{{i.status}}</td>
           <td>{{i.created_at}}</td>   
           <td> 
                  <router-link :to="{ name: 'ImportView', params: { id: i.id } }">
                      <input type="button" class="btn btn-primary m-1" value="Details">  
                    </router-link>           
           
           </td>    
        </tr>
     </table>
     <div v-show="imports.length == 0">
        <p>Brak importów</p>
     </div>
      <AddFile msg="Zaimportuj nowe transakcje" @file-added="onFileAdded" />
  </div>
</template>

<script>
 
import AddFile from './AddFile.vue'
import axios from 'axios'


export default {
  name: 'App',
  data() {
    return {
       imports: []
    }
  },   
  components: {
    AddFile
  },
  methods: {
    getImports : async function() {
        let link = "http://127.0.0.1:8000/api/imports";
        try {
            const response = await axios.get(link);
            return response.data;
       } catch (error) {  
          return error.response.data;  
       }
    },
    onFileAdded: async function() {
        const data = await this.getImports()
        this.imports = data.imports 
    }       
  }, 
async created() {
    const data = await this.getImports()
    this.imports = data.imports
    
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

nav { padding: 20px; background: #f4f4f4; }
nav a { font-weight: bold; color: #2c3e50; margin: 0 10px; }
.router-link-active { color: #42b983 !important; }

</style>
