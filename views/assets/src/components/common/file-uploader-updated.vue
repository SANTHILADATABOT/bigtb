<template>
    <div class="pm-attachment-items">
        <div id="pm-upload-container">
            <div class="pm-upload-filelist">
                <div class="pm-uploaded-item" v-for="file in files" :key="file.id">
                    <div class="attachment-file">
                        <a class="pm-uploaded-img" :href="file.url" target="_blank">
                            <img v-if="file.absoluteUrl" class="pm-uploaded-file" :src="file.absoluteUrl" :alt="file.name" :title="file.name">
                            <img v-if="!file.absoluteUrl" class="pm-uploaded-file" :src="file.thumb" :alt="file.name" :title="file.name">
                        </a> 
                        <span @click.prevent="deletefile(file.id)" class="icon-pm-cross"></span>
                        <!-- <a href="#" @click.prevent="deletefile(file.id)" class=""></a> -->
                    </div>
                    
                    
                        
                </div>
                     
            </div>
        </div>
    </div>
</template>


<style lang="less">
    .pm-attachment-items {
        .pm-uploaded-img {
            display: inline-block;

            .pm-uploaded-file {
                object-fit: contain;
            }
        }
    }
</style>



<script>
    Vue.directive('pm-uploader', {
        inserted: function (el, binding, vnode) { 
            new PM_Uploader(el, 'pm-upload-container', vnode.context );
        },
    });

    Vue.directive('pm-upload-container', {
        inserted: function (el, binding, vnode) { 
        },
    });

    export default {
        props: {
            files: {
                type: Array,
                default: function () {
                    return []
                }
            },
            delete: {
                type: Array,
                default: function () {
                    return []
                }
            },
            single: {
                type: Boolean,
                default: false,
            },
        },

        methods: {
            /**
             * Set the uploaded file
             *
             * @param  object file_res
             *
             * @return void
             */
            fileUploaded: function( file_res ) {

                if ( typeof this.files == 'undefined' ) {
                    this.files = [];
                }

                this.files.push( file_res.res.file );
                
            },

            /**
             * Delete file
             *
             * @param  object file_id
             *
             * @return void
             */
            deletefile: function(file_id) {
                if ( ! confirm( this.text.are_you_sure ) ) {
                    return;
                }
                var self = this;
                var index = self.getIndex(self.files, file_id, 'id');

                if (index !== false) {
                    self.files.splice(index, 1);
                    this.delete.push(file_id);
                }  
            },
        }
    }
</script>
