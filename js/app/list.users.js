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


        result=`<tr data-id="${item.id || ''}">\n` +
            `<td>${item.loginid}</td>\n` +
            `<td>${item.name || item.nickname || ""}</td>\n` +
            `<td>${item.mobile || ""}</td>\n` +
            `<td>${item.last_login_time || ""}</td>\n` +
            `<td>${item.disabled==0? '': '<span class="label label-warning">失效</span>'} ${item.active==1 ? '': '<span class="label label-danger">未激活</span>'}</td>\n` +
            `<td class="btn-group"><button type="button" class="btn btn-info"><i class="glyphicon glyphicon-pencil"></i> 修改</button>\n` +
            `<button type="button" class="btn btn-info"><i class="glyphicon glyphicon-knight"></i> 角色</button>\n` +
            `<button type="button" class="btn btn-info"><i class="glyphicon glyphicon-trash"></i> 删除</button>\n` +
            `</td>\n` +
            `</tr>\n`;
        //result='<tr>'+title+'</tr>';
        return result;
    }//-/

}//==/