class listRoles extends ListDynamic{

    parseParam(params) {
        super.parseParam(params);
        this.url="/api/admin/roles";

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



    getPrivilegeViewHtml(PrivilegeData, htmlStr){

        htmlStr=htmlStr || '';

        let curValue;
        if(typeof PrivilegeData =="object"){

            for(let key  in PrivilegeData){
                if(typeof PrivilegeData[key]=='object'){
                    htmlStr+='<div>'+key+'</div>';
                    htmlStr=this.getPrivilegeViewHtml(PrivilegeData[key],htmlStr);
                }else{
                    curValue='false';
                    if(PrivilegeData[key]==true || PrivilegeData[key]=='true'){
                        curValue='true';
                    }
                    htmlStr+='<div><span>'+key+'</span><b>'+curValue+'</b>';
                }
            }

        }else{
            curValue='false';
            if(PrivilegeData===true){
                curValue='true';
            }
            htmlStr+='<div><span>'+''+'</span><b>'+curValue+'</b>';
        }

        return htmlStr;

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

        let viewPrivilege=this.getPrivilegeViewHtml(item.privileges);

        result=`<tr data-id="${item.id || ''}">\n` +
            `<td>${item.id}</td>\n` +

            `<td>${item.name || ""}</td>\n` +
            `<td>${item.note || ""}</td>\n` +
            `<td>${viewPrivilege || ""}</td>\n` +

            `<td  style="text-align: right"><div class="btn-group"><a type="button" class="btn btn-info btn-edit-user" href="role_add.html?id=${item.id}" target="_blank"><i class="glyphicon glyphicon-pencil"></i> 修改</a>\n` +

            `<a type="button" class="btn btn-info btn-del-user" item-id="${item.id}" item-timestamp="${item.timestamp}"><i class="glyphicon glyphicon-trash"></i> 删除</a>\n` +
            `</div></td>\n` +
            `</tr>\n`;
        //result='<tr>'+title+'</tr>';
        return result;
    }//-/

}//==/