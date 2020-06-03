class ListAjaX extends ListBase{

    parseParam(params) {
        super.parseParam(params);

        //ajax url:
        this.url=params.url || null;

        //ui:
        this.ui=params.ui || null;


        //dataKeyName
        //key-name of list data in result from server by ajax
        //if empty, then get all object attributes and saved in array.
        //   attribute key-name save as element[this.itemKeyName] in array.
        this.dataKeyName=params.dataKeyName || '';


        //itemSavingKeyName: when parse result data in non-array format, data attribute name save in element object by this.itemKeyName.
        this.itemSavingKeyName=params.itemSavingKeyName || 'id';


        //dataParser
        this.resultParser=params.resultParser || this.resultParserDefault;

        //funcView: view function
        this.funcView=params.funcView || this.viewDefault;

        //handleFailed Loading
        this.handleFailed=params.handleFailed || null;

        //handleErr
        this.handleErr=params.handleErr || null;

        //token key name in localStorage.
        this.tokenKeyName=params.tokenKeyName || "token";



    }//-/

    /**
     * resultParserDefault
     * @param resultData
     * @param status
     */
    resultParserDefault(resultData, status){

        let jsonData;


        //try to parse result into json format
        try {

            jsonData = JSON.parse(resultData);
            //console.log("Result-Data ["+postData.target+"]: ");
            console.log(jsonData);


        } catch (err) { //result format is not json

            console.log("Result raw data: ");
            console.log(resultData);

            alert('发生错误，返回错误格式！');
            return false;

        }


        return jsonData;


    }//-/

    /**
     * load
     * load data by ajax
     * @param params
     * @returns {boolean}
     */
    load(params,requestType){

        let SELF = this;




        if(!this.url){
            console.log("no url provided!");
            return false;
        }


        SELF.page=params.page || SELF.page;

        requestType=requestType || 'POST';


        //j79.viewLoading(SELF.ui.list);
        console.log('list post data:');
        console.log(params);

        //communicating with server:
        $.ajax({
            "url":SELF.url,
            "type":requestType,
            "data":params,
            "dataType":"json",
            "contentType":"application/x-www-form-urlencoded",
            "beforeSend":function (XMLHttpRequest) {
                XMLHttpRequest.setRequestHeader("token", localStorage.getItem(SELF.tokenKeyName));
            },
            "success":function(data, txtStatus){
                let jsonData=SELF.resultParser(data,txtStatus)
                if(jsonData!==false){
                    if(SELF.funcView){
                        SELF.funcView(jsonData);
                    }
                }else{
                    if(SELF.handleFailed){
                        SELF.handleFailed(data,txtStatus);
                    }else{
                        console.log("failed loading data!");
                        alert("failed loading data!");
                    }

                }
            },

            "error":function(xmlHR, txtStatus, errThrown){
                if (typeof SELF.handleErr == 'function') {

                    SELF.handleErr(txtStatus,errThrown);

                } else {
                    alert('failed connecting server!');

                }
            }




        });


        /*$.post(SELF.url, params,

            function(data, status) {

                let jsonData=SELF.resultParser(data,status)
                if(jsonData!==false){
                    if(SELF.funcView){
                       SELF.funcView(jsonData);
                    }
                }else{
                    if(SELF.handleFailed){
                        SELF.handleFailed(data,status);
                    }else{
                        console.log("failed loading data!");
                        alert("failed loading data!");
                    }

                }

            }) //-/post
            .error(function(data, status, e) { //error when communicating with server

                /!*console.log(uiTitle + '时，连接服务器出错，请稍后再试');
                console.log('status:'+status);
                console.log('data:');
                console.log(data);
                console.log("error:")
                console.log(e);*!/

                if (typeof SELF.handleErr == 'function') {

                    SELF.handleErr(data,status,e);

                } else {
                    alert('failed connecting server!');

                }
            });*/

    }//-/


    /**
     * viewDefault
     * @param jsonData
     * @param status
     */
    viewDefault(jsonData,status){
        let itemName;
        let dataArray=[];

        if(!this.dataKeyName){

            for(itemName in jsonData){
                if(typeof(jsonData[itemName])=='object'){

                    jsonData[itemName][this.itemSavingKeyName]=itemName;
                    dataArray.push( jsonData[itemName] );
                }
            }

        }else{
            dataArray=jsonData[this.dataKeyName];
        }


        this.data=dataArray;
        let resultHtml=this.generate(this.page);


        if(!this.ui || !this.ui.list){
            return false;
        }

        //clear list:
        $(this.ui.list).children().remove();



        //if list is empty
        if (dataArray.length == 0) {

            $('<div class="list-none"><i class="glyphicon glyphicon-info-sign"></i> 没有结果</div>').appendTo($(this.ui.list));
            if(this.ui.pager){
                $(this.ui.pager).find('.pager-bar').remove();
                return;
            }
        }

        //view list
        for (let i = 0; i < dataArray.length; i++) {

            //append new item to list.
            $(this.itemGenerator(dataArray[i],i)).appendTo($(this.ui.list));

        }

    }//-/




}//==/