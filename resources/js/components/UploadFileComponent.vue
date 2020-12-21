<template>
    <div>
        <div class="large-12 medium-12 small-12 cell">
            <label>
                <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
            </label>
            <iframe style="display: block; width: 90%" v-if="showPdfPreview" :src="preview" frameborder="0" scrolling="no" width="400" height="600"></iframe>
            <img style="display: block; width: 90%"  v-if="showImagePreview" :src="preview"/>
            <input type="hidden" :name="name" :value="value">
        </div>
    </div>
</template>

<script>
    export default {
        props: ['nameProp', 'valueProp'],
        data() {
            return {
                name: this.nameProp,
                value: this.valueProp,
                file: '',
                showImagePreview: false,
                showPdfPreview: false,
                preview: '',
            }
        },
        watch: { 
            valueProp: function(newVal, oldVal) { // watch it
                if (!newVal) {
                    this.showImagePreview = false;
                    this.showPdfPreview = false;
                    this.value = null;
                    this.file = null;
                    this.preview = null;
                }
                console.log('Upload File New Value: ', newVal, ' | was: ', oldVal);
            }
        },
        mounted() {
            let re = /(?:\.([^.]+))?$/;

            let ext = re.exec(this.value)[1];

            if (/\.(jpe?g|png|gif|jpg)$/i.test(this.value)) {
                this.showImagePreview = true;
                this.preview = this.value;
            }

            if (/\.(pdf)$/i.test(this.value)) {
                this.showPdfPreview = true;
                this.preview = this.value;
            }
        },
        methods: {
            submitFile(){
                /*
                        Initialize the form data
                    */
                    let formData = new FormData();

                    /*
                        Add the form data we need to submit
                    */
                    formData.append('file', this.file);

                /*
                Make the request to the POST /single-file URL
                */
                    axios.post( '/api/upload-file',
                        formData,
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                    ).then(response => {
                        if (response.data) {
                            if (response.data.mimeType == 'application/pdf') {
                                this.showPdfPreview = true;
                                this.showImagePreview = false;
                            } else {
                                this.showPdfPreview = false;
                                this.showImagePreview = true;
                            }
                            this.preview = response.data.url;
                            this.value = response.data.url;
                            this.$emit('file', response.data.url);
                        }
                    })
                    .catch(err => {
                        this.spinnerData.loading = false;
                        if (err.response.status === 422) {
                            let errors = err.response.data.errors;
                            if (errors) {
                                Object.keys(errors).forEach(function(name) {
                                    Vue.$toast.error(errors[name][0]);
                                });
                            }
                        }
                        throw err;
                    });
            },

            /*
                Handles a change on the file upload
            */
            handleFileUpload(){
                /*
                Set the local file variable to what the user has selected.
                */
                this.file = this.$refs.file.files[0];
               
                
                /*
                Initialize a File Reader object
                */
                let reader  = new FileReader();
                let formData  = new FormData();
                

                /*
                Add an event listener to the reader that when the file
                has been loaded, we flag the show preview as true and set the
                image to be what was read from the reader.
                */
                reader.addEventListener("load", function () {
                    this.preview = reader.result;
                }.bind(this), false);

                /*
                Check to see if the file is not empty.
                */
                if (this.file) {
                    /*
                        Ensure the file is an image file.
                    */
                    if (/\.(jpe?g|png|gif|jpg)$/i.test(this.file.name)) {
                        /*
                        Fire the readAsDataURL method which will read the file in and
                        upon completion fire a 'load' event which we will listen to and
                        display the image in the preview.
                        */
                        reader.readAsDataURL( this.file );
                        this.showImagePreview = true;
                        this.showPdfPreview = false;
                    }

                    if (this.file.type.indexOf("pdf") >= 0) {
                        this.showPdfPreview = true;
                        this.showImagePreview = false;
                        this.preview = URL.createObjectURL(this.file);
                        formData.append('Файл PDF', this.file, this.file.originalName);
                    }

                    this.submitFile();
                }
            }
        }
    }
</script>

<style scoped>
</style>