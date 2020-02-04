<template>
  <v-dialog v-model="dialog" persistent>
    <v-card>
      <v-toolbar color="#003F96" dark dense flat>
        <v-toolbar-title class="white--text">割り付け</v-toolbar-title>
      </v-toolbar>
      <v-container>
        <ProgressDialog ref="progressDialog" />
        <v-form ref="form">
          <v-text-field v-model="keyname" label="鍵名称" readonly />
          <v-datetime-picker label="開閉可能開始時間" v-model="accept_startdate" clearText="cancel" @input="checkValue"/>
          <v-datetime-picker label="開閉可能終了時間" v-model="accept_enddate" clearText="cancel" @input="checkValue"/>
          <v-list subheader flat>
            <v-subheader>開閉箇所</v-subheader>
            <v-list-item-group v-model="select_accept_locations" multiple color="primary">
              <template v-for="item in accept_locations">
                <v-list-item :value="item.value">
                  <template v-slot:default="{ active, toggle }">
                    <v-list-item-action>
                      <v-checkbox :input-value="active" :true-value="item.value" color="primary" @click="toggle" />
                    </v-list-item-action>
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name" />
                    </v-list-item-content>
                  </template>
                </v-list-item>
              </template>
            </v-list-item-group>
          </v-list>

          <v-card-actions class="pt-0">
            <v-spacer/>
            <v-btn :disabled="this.select_accept_locations.length === 0 || isDisabledUpdateBtn" color="primary darken-1" text @click="update()">Update</v-btn>
            <v-btn color="grey" text @click="close()">Cancel</v-btn>
          </v-card-actions>
        </v-form>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
import ProgressDialog from './ProgressDialog.vue';

export default {
  components: {
    ProgressDialog, 
  },
  data: () => ({
    dialog: false,
    keyname: null,
    keyid: null,
    accept_startdate: null,
    accept_enddate: null,
    cylinder_info: null,
    isDisabledUpdateBtn: false,
    accept_locations: [{ value: '99999900', name: '施設A' }
                       { value: '99999901', name: '施設A（門）' },
                       { value: '99999902', name: '施設A（建屋）' },
                       { value: '99999903', name: '施設A（分電盤）' },
                       { value: '99999904', name: '施設A（門、建屋）' },
                       { value: '88888800', name: '施設B' },
                       { value: '88888801', name: '施設B（ドア）' },
                       { value: '88888802', name: '施設B（分電盤）' }],
    select_accept_locations: [],
  }),
  methods: {
    open(keyinfo) {
      this.dialog = true;
      this.keyname = keyinfo.keyname;
      this.keyid = keyinfo.keyid;
      // 時間表示を現在時刻とその5分後に変更
      // this.accept_startdate = keyinfo.accept_startdate;
      // this.accept_enddate = keyinfo.accept_enddate;
      this.accept_startdate = new Date();
      const v_now = new Date();
      v_now.setMinutes(v_now.getMinutes() + 5);
      this.accept_enddate = v_now;
      this.isDisabledUpdateBtn = false;
      
      this.select_accept_locations = [];
      if (keyinfo.cylinder_info !== null) {
        const v_cylinder_info = keyinfo.cylinder_info.split(',');
        this.select_accept_locations = v_cylinder_info;
      }
      
      this.$nextTick(function() {
        document.getElementsByClassName('v-dialog--active')[0].scrollTop = 0;
      });
    },
    close() {
      this.dialog = false;
    },
    async update() {
      if (this.accept_startdate instanceof Date) {
        this.accept_startdate = this.convertDateformat(this.accept_startdate);
      }
      
      if (this.accept_enddate instanceof Date) {
        this.accept_enddate = this.convertDateformat(this.accept_enddate);
      }
      
      this.cylinder_info = "";
      this.select_accept_locations.sort().forEach((value, index) => {
        if (index !== 0) {
          this.cylinder_info += ",";
        }
        this.cylinder_info += value;
      });
      
      // POST用データ
      const v_postData = {};
      v_postData.keyid = this.keyid;
      v_postData.keyname = this.keyname;
      v_postData.accept_startdate = this.accept_startdate;
      v_postData.accept_enddate = this.accept_enddate;
      v_postData.cylinder_info = this.cylinder_info;
      
      this.$refs.progressDialog.open();
      
      try {
        const response = await axios.post('/api/update', v_postData);
        setTimeout( () => {
          if (response.status === 200) {
            console.log('OK');
            this.$emit('callParent');
          }
          this.$refs.progressDialog.close();
          this.dialog = false;
        }, 6000);
      }
      catch {
        console.log('error');
      }
    },
    convertDateformat(v_date) {
      const year = v_date.getFullYear();
      let month = (v_date.getMonth() + 1);
      let day = v_date.getDate();
      let hours = v_date.getHours();
      let minutes = v_date.getMinutes();
      
      if (month < 10) {
        month = '0' + month;
      }
     
      if (day < 10) {
        day = '0' + day;
      }
      
      if (hours < 10) {
        hours = '0' + hours;
      }
      
      if (minutes < 10) {
        minutes = '0' + minutes;
      }
      
      return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
    },
    checkValue() {
      this.isDisabledUpdateBtn = false;
      
      if (this.accept_startdate === null || this.accept_enddate === null || new Date(this.accept_startdate) >= new Date(this.accept_enddate)) {
        this.isDisabledUpdateBtn = true;
        return;
      }
    },
  },
}
</script>
