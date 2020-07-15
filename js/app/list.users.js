class listUsers extends ListDynamic{

    parseParam(params) {
        super.parseParam(params);
        this.url="/api/admin/users";

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
            tt=tt.toString().replace('T',' ');
            tt=tt.replace('.000Z','');
            item.timestamp=tt;
        }

        result=`<tr data-id="${item.id || ''}">\n` +
            `<td>${item.id}</td>\n` +
            `<td>${item.loginid}</td>\n` +
            `<td>${item.name || item.nickname || ""}</td>\n` +
            `<td>${item.mobile || ""}</td>\n` +
            `<td>${item.last_login_time || ""}</td>\n` +
            `<td>${item.disabled==0? '': '<span class="label label-warning">失效</span>'} ${item.active==1 ? '': '<span class="label label-danger">未激活</span>'}</td>\n` +
            `<td class="btn-group"><a type="button" class="btn btn-info btn-edit-user" href="user_add.html?id=${item.id}" target="_blank"><i class="glyphicon glyphicon-pencil"></i> 修改</a>\n` +
            `<a type="button" class="btn btn-info btn-modify-role"><i class="glyphicon glyphicon-knight"></i> 角色</a>\n` +
            `<a type="button" class="btn btn-info btn-del-user" item-id="${item.id}" item-timestamp="${item.timestamp}"><i class="glyphicon glyphicon-trash"></i> 删除</a>\n` +
            `</td>\n` +
            `</tr>\n`;
        //result='<tr>'+title+'</tr>';
        return result;
    }//-/

}//==/