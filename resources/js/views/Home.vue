<template>
  <v-container>
    <v-form ref="form">
      <v-data-table :headers="keyListHeaders" :items="keyListItems" class="elevation-1">
        <template v-slot:item="props">
          <tr>
            <td class="text-xs-left">{{ props.item.keyname }}</td>
            <td class="text-xs-left">{{ props.item.accept_period }}</td>
            <td class="justify-center layout px-0">
              <v-icon small class="mr-2" @click="editKeyInfo(props.item.index)">edit</v-icon>
            </td>
          </tr>
        </template>
      </v-data-table>
    </v-form>
    <ProgressDialog ref="progressDialog" />
    <EditKeyInfoDialog ref="editKeyInfoDialog" @nameChanged="editKeyInfoChanged" @callParent="getKeyList"/>
  </v-container>
</template>

<script>
import EditKeyInfoDialog from '../components/EditKeyInfoDialog.vue'
import ProgressDialog from '../components/ProgressDialog.vue'

export default {
  components: {
    EditKeyInfoDialog, 
    ProgressDialog,
  },
  data: () => ({
    keyList: [],
    keyListItems: [],
    keyListHeaders: [{ text: '鍵名称', value: 'keyname', align: 'left' },
                     { text: '開閉可能時間', value: 'accept_period', align: 'left' },
                     { text: '操作', value: 'action', align: 'center', sortable: false }],
  }),
  methods: {
    async getKeyList() {
      try {
        this.$refs.progressDialog.open();
        const response = await axios.get('/api/select');
        this.keyList = response.data;
        this.keyListItems = [];
        this.keyList.forEach((value, index) => {
          const v_startdatetime = value.accept_startdate.split(' ');
          const v_enddatetime = value.accept_enddate.split(' ');
          const v_startdate = v_startdatetime[0].split('-');
          const v_enddate = v_enddatetime[0].split('-');
          this.keyListItems.push(value);
          this.keyListItems[index].index = index;
          this.keyListItems[index].accept_period = v_startdate[0] + '年' + v_startdate[1] + '月' + v_startdate[2] + '日 ' + v_startdatetime[1]
                                                 + ' ～ ' + v_enddate[0] + '年' + v_enddate[1] + '月' + v_enddate[2] + '日 ' + v_enddatetime[1];
        });
      }
      catch {
        console.log('error');
      }
      finally {
        this.$refs.progressDialog.close();
      }
    },
    async editKeyInfo(keyIndex) {
      await this.$refs.editKeyInfoDialog.open(this.keyList[keyIndex]);
    },
    async editKeyInfoChanged(updateItem) {
      this.getKeyList();
    }
  },
  async mounted() {
    this.getKeyList();
  }
}
</script>
