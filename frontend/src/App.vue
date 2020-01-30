<template>
  <div id="app">
    <div class="container-fluid">

      <div class="row">

        <div class="col-lg-12">

          <h1>Test project written with</h1>
          <h1><code><a target="_blank" href="https://bootstrap-vue.js.org/">Bootstrap Vue |</a>  <a target="_blank" href="https://vuejs.org/">VueJS v2 |</a>  <a target="_blank" href="https://github.com/xaksis/vue-good-table#readme">Vue Good Table |</a> and PHP 7.2+</code></h1>
          <p class="lead">Created by <a target="_blank" href="https://www.linkedin.com/in/volodymyr-sitdikov-499736123/">Vladimir Sitdikov</a></p>

          <b-jumbotron>
            <p class="lead">A little bit explanation: You allowed to submit pre-built models.
              You can add any models into folder <code>app/object</code>, one class per one file. After you add models they will allowed
            to be selected to submission.</p>
            <p class="lead">Also application allowing to change storage engines.
              Default is <code>file</code> which obviously stores all the data inside files,
              but you could change it to <code>sqlite</code> inside <code>.env</code> file to store it inside sqlite database.
              You can write any storage just make it implement <code>StorageInterface and StructureInterface</code>.
              <code>StorageInterface</code> used to store data. <code>StructureInterface</code> needed in order to send data to frontend, so you could
              write many <code>StructureInterface</code> classes in order to provide data to different clients that demands data in different structured ways.
            </p>
          </b-jumbotron>

          <div class="space"></div>
          <div class="row">
            <div class="mx-auto col-lg-8">
              <b-form>
                <b-row>
                  <b-col class="mx-auto col-lg-12">
                    <b-row class="mb-5">
                      <b-col sm="6">
                        <b-input-group>
                          <b-input-group-prepend>
                            <b-input-group-text>
                              Select submission type:
                            </b-input-group-text>
                          </b-input-group-prepend>
                          <b-form-select
                            v-model="type"
                            :options="submissionTypes"
                          >
                            <template v-slot:first>
                              <b-form-select-option :value="null" disabled>Please select an option</b-form-select-option>
                            </template>
                          </b-form-select>
                        </b-input-group>
                      </b-col>

                      <b-col sm="6">
                        <b-input-group>
                          <b-input-group-prepend>
                            <b-input-group-text>
                              How many objects:
                            </b-input-group-text>
                          </b-input-group-prepend>
                          <b-form-select
                            v-model="quantity"
                            :options="[1,2,3,4,5,6,7,8,9,10]"
                          >
                            <template v-slot:first>
                              <b-form-select-option :value="null" disabled>Please select quantity</b-form-select-option>
                            </template>
                          </b-form-select>
                        </b-input-group>
                      </b-col>

                    </b-row>
                  </b-col>
                </b-row>

                <b-form-group v-if="type === 'built-in'">
                  <b-row v-if="type === 'built-in'" class="mb-5">
                    <b-col sm="12" class="mx-auto ">
                      <b-input-group>
                        <b-input-group-prepend>
                          <b-input-group-text>
                            Select model:
                          </b-input-group-text>
                        </b-input-group-prepend>
                        <b-form-select
                          v-model="builtInModel"
                          :options="builtInModels"
                        >
                          <template v-slot:first>
                            <b-form-select-option :value="null" disabled>Please select a model</b-form-select-option>
                          </template>
                        </b-form-select>
                      </b-input-group>
                    </b-col>
                  </b-row>
                  <b-row>
                    <b-col sm="6">
                    </b-col>
                    <b-col sm="6">
                      <b-button :disabled="!builtInModel || !quantity" block variant="success" @click="submitBuiltIn">Submit</b-button>
                    </b-col>
                  </b-row>
                </b-form-group>
                <b-form-group v-else-if="type === 'object'">
                  <b-row v-for="(prop, index) in objectProperties" v-bind:key="index" class="mb-5">
                    <b-col sm="6">
                      <b-input-group>
                        <b-input-group-prepend>
                          <b-input-group-text>
                            Key:
                          </b-input-group-text>
                        </b-input-group-prepend>
                        <b-form-input
                          placeholder="Enter key"
                          v-model="prop.key"
                        ></b-form-input>
                      </b-input-group>
                    </b-col>
                    <b-col sm="5">
                      <b-input-group>
                        <b-input-group-prepend>
                          <b-input-group-text>
                            Value:
                          </b-input-group-text>
                        </b-input-group-prepend>
                        <b-form-input
                          placeholder="Enter value"
                          v-model="prop.value"
                        ></b-form-input>
                      </b-input-group>
                    </b-col>
                    <b-col sm="1" class="text-left">
                      <b-button size="sm" v-if="index === objectProperties.length - 1 && objectProperties.length > 1 || index !== objectProperties.length - 1" class="remove-button" variant="danger" @click="removeProp(index)">-</b-button>
                      <b-button size="sm" v-if="index === objectProperties.length - 1" variant="success" @click="addMoreProp" class="add-button">+</b-button>
                    </b-col>
                  </b-row>
                  <b-row>
                    <b-col sm="6">
                    </b-col>
                    <b-col sm="6">
                      <b-button :disabled="objectHasEmptyKeys || !quantity" block variant="success" @click="submitObject">Submit</b-button>
                    </b-col>
                  </b-row>
                </b-form-group>
              </b-form>



            </div>
          </div>
          <div class="space"></div>
        </div>

      </div>
      <representation :rows="rows"></representation>
      <b-row class="mt-4">
        <b-col sm="6">
        </b-col>
        <b-col sm="6">
          <b-button block variant="danger" @click="flush">Flush all data</b-button>
        </b-col>
      </b-row>
      <div class="space"></div>
    </div>
  </div>
</template>

<script>
import Representation from './components/representation';
import axios from 'axios';
export default {
  name: 'App',
    mounted(){
      this.getRows();
    },
    methods: {
        flush(){
            var that = this;
            axios.post(this.mainServer + '/server.php', {action: 'flush'}).then(function(answer){
                that.rows = answer.data;
            });
        },
        submitBuiltIn(){
            var that = this;
            axios.post(this.mainServer + '/server.php', {action: 'built-in', data: {model: this.builtInModel, quantity: this.quantity}}).then(function(answer){
                that.rows = answer.data;
            });
        },
        submitObject(){
            var that = this;
            axios.post(this.mainServer + '/server.php', {action: 'object', data: {properties: this.objectProperties, quantity: this.quantity}}).then(function(answer){
                that.rows = answer.data;
            });
          },
        addMoreProp(){
            this.objectProperties.push({key: '', value: ''});
        },
        removeProp(index){
            this.objectProperties.splice(index, 1);
        },
        getRows(){
            var that = this;
            axios.get(this.mainServer + '/server.php').then(function(answer){
                that.rows = answer.data;
            });
            axios.get(this.mainServer + '/server.php', {params: {action: 'getModels'}}).then(function(answer){
                that.builtInModels = answer.data;
            });
        },
    },
    computed: {
        objectHasEmptyKeys(){
            return !!this.objectProperties.find(function(element){
                if(!element.key)
                    return element;
            })
        },
    },
    data(){
        return {
            mainServer: '',
            objectProperties: [
                {key: '', value: ''},
            ],
            type          : null,
            quantity      : null,
            builtInModel  : null,
            builtInModels : [],
            submissionTypes         : [
                  { value: 'built-in', text: 'Built-in models' },
                  { value: 'object', text: 'PHP object' }
            ],
            rows: [],
        };
    },
  components: {
      Representation,
  }
}
</script>

<style>
  #app {
    text-align: center;
    margin-top: 120px;
  }
  .space {
    width: 100%;
    height: 100px;
  }
  .remove-button, .add-button {
    width: 26px;
    height: 26px;
    line-height: 10px;
    margin-top: 6px;
  }
</style>
