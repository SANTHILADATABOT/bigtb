<template>
    <div v-if="total_pages > 1" class="pm-pagination-wrap">
        <div class="pm-pagination-inner" >
            
            <router-link 
                v-if="parseInt(current_page_number) > 1" 
                class="pm-pagination-btn prev page-numbers" 
                :to="{ 
                    name: component_name,  
                    params: setParams(parseInt(current_page_number) - 1),
                    query: route_query
                }">
                &larr;
            </router-link>
            
            <router-link 
                :key="page" 
                v-for="page in total_pages" 
                :class="pageClass(page) + ' pm-pagination-btn'" 
                :to="{ 
                    name: component_name,
                    params: setParams(page),
                    query: route_query
                }">
                {{ page }}
            </router-link>
            
            <router-link 
                v-if="parseInt(current_page_number) < parseInt(total_pages)" 
                class="pm-pagination-btn next page-numbers" 
                :to="{ 
                    name: component_name,  
                    params: setParams(parseInt(current_page_number) + 1),
                    query: route_query
                }">
                &rarr;
            </router-link> 

        </div>
        <div class="pm-clearfix"></div>
    </div>
</template>

<script>
    export default {
        props: {
            total_pages: {
                type: [String, Number]
            },
            current_page_number: {
                type: [String, Number]
            },
            component_name: {
                type: [String]
            }
        },

        data () {
            return {
                route_query: this.$route.query
            }
        },

        watch: {
            '$route' (url) {
                this.route_query = url.query;
            },
        },

        methods: {
            setParams ( current_page_number ) {
                var params = Object.assign({}, this.$route.params);
                params.current_page_number = current_page_number;
                
                return params;
            },
            pageClass: function( page ) {
                if ( page == this.current_page_number ) {
                    return 'page-numbers current'
                }

                return 'page-numbers';
            },
        }
    }
</script>
