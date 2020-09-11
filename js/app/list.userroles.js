class listUserRoles extends ListDynamic{

    parseParam(params) {
        super.parseParam(params);
        this.url="/api/admin/roleallots";

        //set paged by self.
        this.pageType=0;


    }//-/


    /**
     * getDataTransporter
     * @param params
     * @returns {dataTransporter}
     */
    getDataTransporter(params){
        //need overwrite in subclass
        return new dataTransporter(params);
    }





    /**
     * itemGeneratorDefault
     * @param item
     * @param listIndex
     * @returns {string}
     */
    itemGeneratorDefault(item, listIndex){
        let result='';

        let tt=item.timestamp;
        if(tt){
            /*tt=tt.toString().replace('T',' ');
            tt=tt.replace('.000Z','');
            item.timestamp=tt;*/
        }



        result=`<tr data-id="${item.id || ''}">\n` +
            `<td>${item.id}</td>\n` +

            `<td>${item.username || ""}</td>\n` +


            `<td>${item.roleid || ""}</td>\n` +
            `<td>${item.rolename || ""}</td>\n` +


            `<td  style="text-align: right"><div class="btn-group">` +

            `<a type="button" class="btn btn-info btn-del-user" item-id="${item.id}" item-timestamp="${item.timestamp}"><i class="glyphicon glyphicon-trash"></i> 删除</a>\n` +
            `</div></td>\n` +
            `</tr>\n`;
        //result='<tr>'+title+'</tr>';
        return result;
    }//-/

}//==/