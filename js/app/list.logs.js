class listLogs extends ListDynamic{

    parseParam(params) {
        super.parseParam(params);
        this.url="/api/admin/actlogs";

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
            `<td>${item.id}</td>\n` +
            `<td>${item.username}(${item.userid})</td>\n` +
            `<td>${item.target}</td>\n` +
            `<td>${item.action}</td>\n` +
            `<td>${item.message}</td>\n` +
            `<td>${item.timestamp}</td>\n` +

            `</tr>\n`;
        //result='<tr>'+title+'</tr>';
        return result;
    }//-/

}//==/