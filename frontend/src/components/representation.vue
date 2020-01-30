<template>
  <div>
    <div class="row mb-5">
      <b-col sm="6">
      </b-col>
      <b-col sm="6">
        <b-input-group>
          <b-input-group-prepend>
            <b-input-group-text>
              Table view plugin:
            </b-input-group-text>
          </b-input-group-prepend>
          <b-form-select
            v-model="tablePlugin"
            :options="['Vue Good Table', 'Vue Tables 2']"
          >
          </b-form-select>
        </b-input-group>
      </b-col>
    </div>
    <div class="row">
      <div class="col-lg-12">
        <vue-good-table v-if="tablePlugin === 'Vue Good Table'"
                        :pagination-options="pagination"
                        :columns="columns"
                        :rows="rows"/>
        <v-client-table v-if="tablePlugin === 'Vue Tables 2'" :data="rows" :columns="columns2" :options="options"/>
      </div>
    </div>
  </div>
</template>

<script>
    import { VueGoodTable } from 'vue-good-table';
    export default {
        props: {
            rows: {default: []},
        },
        methods: {
        },
        data(){
            return {
                tablePlugin: 'Vue Good Table',
                options: {
                    filterable: false,
                    templates: {
                        response: function(h, row, index){
                            return JSON.stringify(row.response);
                        },
                        metadata: function(h, row, index){
                            return JSON.stringify(row.response.metadata);
                        },
                        userAgent: function(h, row, index){
                            return JSON.stringify(row.response.metadata.user_agent);
                        },
                    }
                },
                pagination: {
                    enabled: true,
                    mode: 'records',
                    perPage: 10,
                    position: 'top',
                    perPageDropdown: [3, 7, 9],
                    dropdownAllowAll: true,
                    setCurrentPage: 1,
                    nextLabel: 'next',
                    prevLabel: 'prev',
                    rowsPerPageLabel: 'Rows per page',
                    ofLabel: 'of',
                    pageLabel: 'page', // for 'pages' mode
                    allLabel: 'All',
                },
                columns: [
                    {
                        label: 'Number',
                        field: 'id',
                        type: 'number',
                    },
                    {
                        label: 'Full response',
                        field: 'response',
                        sortable: false,
                    },
                    {
                        label: 'Data',
                        field: 'data',
                        sortable: false,
                    },
                    {
                        label: 'Metadata',
                        field: 'metadata',
                        sortable: false,
                    },
                    {
                        label: 'Received at',
                        field: 'received_at',
                        type: 'date',
                        dateInputFormat: 't',
                        dateOutputFormat: 'yyyy-MM-dd',
                    },
                ],
                columns2: ['id', 'response', 'metadata', 'userAgent'],
            };
        },
        components: {
            VueGoodTable,
        }
    }
</script>

<style scoped>

</style>
