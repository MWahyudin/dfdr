<template>
  <div>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">All Deposit</h1>
      <div>
        <router-link
          :to="{ name: 'UploadFile' }"
          class="btn btn-sm btn-primary shadow-sm"
          ><i class="fas fa-plus fa-sm text-white-50"></i>&nbsp; Add
          New</router-link
        >
        <router-link
          :to="{ name: 'ReportPdf' }"
          class="btn btn-sm btn-secondary shadow-sm"
          ><i class="fas fa-cube fa-sm text-white-50"></i>&nbsp;
          Report</router-link
        >
      </div>
    </div>
    <!-- table -->
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <b-row class="mb-3">
              <b-col md="3">
                <b-form-input
                  v-model="filter"
                  type="search"
                  id="filterInput"
                  placeholder="Type to Search"
                ></b-form-input>
              </b-col>
              <b-col md="9">
                <!-- <a
                  href="/api/upload-form"
                  target="_blank"
                  class="d-none d-sm-inline-block btn  btn-primary shadow-sm float-right"
                  ><i class="fas fa-plus fa-sm text-white-50"></i> Generate
                  JSON</a
                > -->
                <form>
                  <button
                    type="submit"
                    class="btn btn-sm btn-primary float-right mr-1"
                    v-on:click.prevent="onRegister()"
                  >
                    <i class="fas fa-upload fa-sm text-white-50"></i>&nbsp;
                    Import
                  </button>
                  <input
                    type="file"
                    class="float-right"
                    id="upload_card"
                    ref="file1"
                    aria-describedby="fileError"
                    v-on:change="handleUploadCard()"
                  />
                  <small
                    id="fileError"
                    class="form-text text-muted"
                    v-if="submitted && !$v.form.fileExcel.required"
                    >Harap upload excel file</small
                  >
                  <small
                    id="fileError"
                    class="form-text text-muted"
                    v-if="
                      $v.form.fileExcel.required && wrongFormat & !submitted
                    "
                    ><i class="fa fa-times"></i>&nbsp; Format file tidak
                    didukung</small
                  >
                </form>
              </b-col>
            </b-row>
            <b-table
              hover
              responsive
              :items="items"
              :fields="fields"
              :per-page="perPage"
              :current-page="currentPage"
              :filter="filter"
              show-empty
            >
              <template #cell(descriptions)="data">
                <div v-html="data.item.description"></div>
              </template>
              <template #cell(status)="data">
                <span
                  v-if="data.item.status == 'Approved'"
                  class="badge badge-success"
                >
                  Approved
                </span>
                <span
                  v-if="data.item.status == 'pending'"
                  class="badge badge-warning"
                >
                  Pending
                </span>
                <span
                  v-if="data.item.status == 'Reject'"
                  class="badge badge-danger"
                >
                  Reject
                </span>
              </template>
              <template #cell(publisher)="data">
                {{ data.item.users.name }}
              </template>
              <template #empty="scope">
                <h4>{{ scope.emptyText }}</h4>
              </template>
              <template #emptyfiltered="scope">
                <h4>{{ scope.emptyFilteredText }}</h4>
              </template>
              <template v-slot:cell(file)="data">
                <!-- <b-button v-b-modal.modal-1>Launch demo modal</b-button>
  v-on:click.prevent="downloadedFile(data.item.file_name, data.item.id)"
  <b-modal id="modal-1" title="BootstrapVue">
    <p class="my-4">Hello from modal!</p>
  </b-modal> -->
                <b-button
                  variant="primary"
                  class="btn-sm"
                  v-b-tooltip.hover
                  title="Open file"
                  @click="openModal(data.item.upload_form_file)"
                  ><i class="fas fa-fw fa-file"></i
                ></b-button>
                 <b-button
                  variant="secondary"
                  class="btn-sm"
                  v-b-tooltip.hover
                  title="Open folderr"
                  @click="openDir(data.item.file)"
                  ><i class="fas fa-fw fa-folder"></i
                ></b-button>
                <b-modal
                  v-model="showModal"
                  hide-backdrop 
                  content-class="shadow"
                  :scrollable="true"
                  title="Daftar file"
                  header-bg-variant="primary"
                  header-text-variant="light"
                >
                 <b-table :fields="fieldModals" :items="selectedFiles">
      <template #cell(file)="data">
        <!-- `data.value` is the value after formatted by the Formatter -->
     <div v-if="data.value.includes('/storage/uploads/file_upload/')">
          
      <a :href="`${data.value}`" target="_blank">
            <b-button
                  variant="success"
                  class="btn-sm"
                  v-b-tooltip.hover
                  title="Download file"
                 
                  ><i class="fas fa-fw fa-download"></i
                ></b-button>
        </a>
     </div>
     <div v-else>

         <b-button
                  variant="success"
                  class="btn-sm"
                  v-b-tooltip.hover
                  title="Download file"
                 @click="getFileApi(data.value)"
                  ><i class="fas fa-fw fa-download"></i
                ></b-button>
         
     </div>
      </template>
    </b-table>

                  <template #modal-footer>
                    <div class="w-100"></div>
                  </template>
                </b-modal>
              </template>
              <template v-slot:cell(actions)="data">
                <b-button
                  variant="success"
                  class="btn-sm"
                  v-b-tooltip.hover
                  title="Detail"
                  v-on:click="toDetail(data.item.id)"
                  ><i class="fas fa-fw fa-edit"></i
                ></b-button>
                <b-button
                  variant="danger"
                  class="btn-sm"
                  v-b-tooltip.hover
                  title="Delete"
                  v-on:click="toDelete(data.item.id)"
                  ><i class="fas fa-fw fa-trash-alt"></i
                ></b-button>
              </template>
            </b-table>
            <b-pagination
              pills
              v-model="currentPage"
              :total-rows="rows"
              :per-page="perPage"
              aria-controls="my-table"
              change="handlePaging($event)"
            ></b-pagination>
          </div>
        </div>
      </div>
    </div>
    <ul style="display: hidden">
      <li v-for="(user, u) in uploads" :key="u">
        {{ user.title }}
      </li>
    </ul>
    <!-- modal -->
    <!-- <b-modal id="modal-1" title="Detail User" size="xl" :hide-footer="true">
      <user-management-detail-component
        :id_user="id_user"
      ></user-management-detail-component>
    </b-modal> -->
  </div>
</template>
<script>
import { required, minLength, email } from "vuelidate/lib/validators";
export default {
  data() {
    return {
      fileName : null,
       fieldModals: [
          {
            // A column that needs custom formatting,
            // calling formatter 'fullName' in this app
            key: 'file_name',
            label: 'Nama',
            // formatter: 'fullName'
          },
          // A regular column
        {
           key: 'file_extention',
            label: 'Ekstensi',
            // formatter: 'fullName'
        },
         {
           key: 'file_size',
            label: 'Ukuran',
            // formatter: 'fullName'
        },
      
          {
           key: 'file',
            label: 'Aksi',
            formatter: value => {
              let is_private = value.includes('private_file')
               var filename = value.substring(value.lastIndexOf('/')+1);
        if(is_private === true) {
          // this.fileName = filename;
          return filename;
        }
           else {
         return  "/storage/uploads/file_upload/" + filename;
             
        // return is_private;
            }
            }
        },
          // {
          //   // A regular column with custom formatter
          //   key: 'sex',
          //   formatter: value => {
          //     return value.charAt(0).toUpperCase()
          //   }
          // },
          // {
          //   // A virtual column with custom formatter
          //   key: 'birthYear',
          //   label: 'Calculated Birth Year',
          //   formatter: (value, key, item) => {
          //     return new Date().getFullYear() - item.age
          //   }
          // }
        ],
      form: {
        fileExcel: "",
      },
      isGenerated: false,
      linkDownload: "",
      submitted: false,
      wrongFormat: false,

      id_user: "",
      filter: "",
      perPage: 10,
      currentPage: 1,
      fields: [
        "title",
        "category",
        "descriptions",
        "year",
        "creator",
        "publisher",
        "status",
        "file",
        "actions",
      ],
      items: [],
      showModal: false,
      selectedFiles: [],
    };
  },
  validations: {
    form: {
      fileExcel: { required },
    },
  },
  computed: {
    rows() {
      return this.items.length;
    },
    uploads() {
      this.items = this.$store.getters.getUploadedFile;
    },
  },
  mounted() {
    if (localStorage.getItem("role") == "User") {
      this.$router.push("/my-upload");
    }
    this.$store.dispatch("getUploadedFile");
  },
  filters: {
    truncate: function(text, length, suffix) {
      if (text.length > length) {
        return text.substring(0, length) + suffix;
      } else {
        return text;
      }
    },
  },
  methods: {
    getFileApi(filename) {
      axios
        .get("/api/file/" + filename , {
          responseType: "blob"
        })
        .then((response) => {
          console.log(response.data);
          //  var headers = response.headers();
     var blob = new Blob([response.data]);
     var link = document.createElement('a');
     link.href = window.URL.createObjectURL(blob);
     link.download = filename;
     link.click();
     link.remove();
   
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    openDir(path){
console.log(path);
      // window.open(path, '_blank');
       window.open(`public/storage/uploads/files/repository/${path}`, '_blank');
    
    },
    fullName(){
      return 'abc';
    },

    getLink(link){
      var filename = link.substring(link.lastIndexOf('/')+1);
        let file_path = "/storage/uploads/file_upload/" + filename;
        return "ABHC";
            // var a = document.createElement("A");
            // a.href = file_path;
            // return file_path.substr(file_path.lastIndexOf("/") + 1);  
            // var file_path = "/storage/uploads/file_upload/" + filename;
            // var a = document.createElement("A");
            // a.href = file_path;
            // a.download = file_path.substr(file_path.lastIndexOf("/") + 1);
    },
    openModal(files) {
      console.log(files);
      this.showModal = true;
      this.selectedFiles = files;
    },
    handleUploadCard() {
      // this.form.fileExcel = this.$refs.file1.files[0];
      // console.log();

      if (this.$refs.file1.files.length != 0) {
        this.form.fileExcel = this.$refs.file1.files[0];
        var type = this.form.fileExcel.type;

        if (
          type ===
          "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        )
          this.wrongFormat = false;
        else this.wrongFormat = true;
      } else {
        this.wrongFormat = false;
      }
    },
    onRegister() {
      this.submitted = true;
      var vm = this;
      // this.$v.$touch();
      // if (this.$v.$invalid) {
      //   return;
      // }
      let formData = new FormData();
      formData.append("file", this.form.fileExcel);
      axios
        .post("/api/excel/import/report", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        })
        .then(function() {
          Vue.swal({
            icon: "success",
            title: "Bulk excel Berhasil!",
          });
          // this.$store.dispatch("getUploadedFile");

          vm.$store.dispatch("getUploadedFile");
          // vm.$router.push("/editor-management");
          // this.clearData();
          console.log("SUCCESS!!");
        })
        .catch(function(error) {
          Vue.swal({
            icon: "error",
            title: "Import excel Gagal!",
            text:
              "Mohon perhatikan kembali format excel atau jumlah data input harus lebih dari 5 data",
          });
          console.log(error);
        });
    },
    downloadedFile(filename, id) {
      let formData = {
        click: "Download",
      };

      axios.post("/api/upload-form/increments/" + id, formData).then((res) => {
        console.log(res.data.data);
        if (res) {
          if (filename != null) {
            var file_path = "/storage/uploads/file_upload/" + filename;
            var a = document.createElement("A");
            a.href = file_path;
            a.download = file_path.substr(file_path.lastIndexOf("/") + 1);
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
          }

          // this.reGetSubmission();
        }
      });
    },

    readFile(filename, id) {
      // --- view PDF with browser default setting & without IDM force download (On Page) ---
      // var file_path = "/storage/uploads/file_upload/" + filename.file_name;
      // var obj = document.createElement('object');
      // obj.style.width = '100%';
      // obj.style.height = '530pt';
      // obj.type = 'application/pdf';
      // obj.data = file_path;
      // window.open(obj);
      // document.getElementById("pdf-view").appendChild(obj);

      let formData = {
        click: "View",
      };

      axios.post("/api/upload-form/increments/" + id, formData).then((res) => {
        console.log(res.data.data);
        if (res) {
          if (filename != null) {
            // --- view PDF with browser default setting & without IDM force download (Off Page) ---
            var file_path = "/storage/uploads/file_upload/" + filename;
            window.open(file_path, "fullscreen=yes");
          }
          // this.reGetSubmission();
        }
      });
    },

    toDetail(id) {
      const vm = this;
      vm.$router.push("/detail-upload/" + id);
    },

    toDelete(id) {
      Vue.swal({
        title: "Delete Data",
        text: "Are you sure want to delete this data permanently?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "secondary",
        confirmButtonText: "Delete",
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete("/api/upload-form/" + id).then((res) => {
            Vue.swal({
              icon: "success",
              title: "Successfully Deleted!",
            });
            this.$store.dispatch("getUploadedFile");
          });
        }
      });

      // axios.delete("/api/upload-form/" + id).then((res) => {
      //   Vue.swal({
      //     title: "Hapus Data?",
      //     text: "Data yang sudah dihapus tidak bisa dikembalikan lagi!",
      //     icon: "warning",
      //     showCancelButton: true,
      //     confirmButtonColor: "#3085d6",
      //     cancelButtonColor: "#d33",
      //     confirmButtonText: "Hapus",
      //   }).then((result) => {
      //     if (result.isConfirmed) {
      //       console.log("bisa");
      //       this.$store.dispatch("getUploadedFile");
      //       Vue.swal("Deleted!", "Your data has been deleted.", "success");
      //     }
      //   });
      // });
    },
  },
};
</script>
