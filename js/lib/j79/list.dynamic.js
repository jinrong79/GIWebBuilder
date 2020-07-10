class ListDynamic extends ListBase{

    parseParam(params) {
        super.parseParam(params);

        //ajax url:
        this.url=params.url || null;



        //page type:
        //          0- set paged by self. 1- set paged by server;
        this.pageType=params.pageType || 0;



        //dataKeyName
        //key-name of list data in result from server by ajax
        //if empty, then get all object attributes and saved in array.
        //   attribute key-name save as element[this.itemSavingKeyName] in array.
        //if not empty, then get resultData[this.dataKeyName] as listData.
        this.dataKeyName=params.dataKeyName || 'data';


        //itemSavingKeyName: when parse result data in non-array format, data attribute name save in element object by this.itemSavingKeyName.
        this.itemSavingKeyName=params.itemSavingKeyName || 'id';


        //dataParser
        this.resultParser=params.resultParser || this.resultParserDefault;

        //funcView: view function
        this.funcView=params.funcView || this.viewDefault;

        //handleFailed Loading
        this.handleFailed=params.failed || null;

        //call when load and view list finished.
        this.callAfterLoad=null;



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
     * getDataTransporter
     * should overwrite in subclass to set correct dataTransporter class.
     * @param params
     */
    getDataTransporter(params){
        //need overwrite in subclass
        return new dataTransporterBase(params);
    }//-/

    /**
     * load
     * load data by dataTransporter
     * @param params
     * @returns {boolean}
     */
    load(params,requestType){

        let SELF = this;

        params=params || {};

        if(!this.url){
            console.log("no url provided!");
            return false;
        }



        requestType=requestType || 'GET';
        SELF.callAfterLoad=params.callAfterLoad || null;

        let loadPage=params.page || SELF.page;


        //j79.viewLoading(SELF.ui.list);
        console.log('list post data:');
        console.log(params);

        let requestData={};

        if(SELF.pageType==1){
            requestData.page=loadPage;
        }else{
            SELF.page=1;
        }


        let dataTranporter=this.getDataTransporter();
        dataTranporter.dataGet({
            "url":SELF.url,
            "requestType":requestType,
            "success":function(data){




                //set current page by request page number when paging is done by server:
                if(SELF.pageType==1){
                    SELF.page=loadPage;
                }

                //view list
                if(SELF.funcView){
                    SELF.funcView(data);
                }
                //call after load and view list:
                if(SELF.callAfterLoad){
                    SELF.callAfterLoad(SELF);
                }


            },
            "failed":function(code,msg,xmlHR){

                if (typeof SELF.handleFailed == 'function') {

                    SELF.handleFailed(code,msg,xmlHR);

                } else {
                    alert('failed connecting server!');

                }

                /*console.log(code);
                console.log(msg);
                console.log(xmlHR);*/
            }
        });

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

        //set data:
        this.data=dataArray;

        //view:
        this.view(this.page);

        //view pager:
        if(this.ui && this.ui.pager){
            j79.viewPager(this.pageTotal, this.page, this.ui.pager, this, this.itemAmount);
        }

    }//-/

    /**
     * view
     * view list by page.
     * @param curPage
     * @returns {boolean}
     */
    view(curPage){

        if(!this.ui || !this.ui.list){
            return false;
        }

        //validate curPage:
        curPage=curPage<=0 ? 1: curPage;



        //generate list html.
        let resultHtml=this.generate(curPage);

        //clear list:
        $(this.ui.list).children().remove();

        //if list is empty
        if (this.data.length <= 0) {

            $('<div class="list-none"><i class="glyphicon glyphicon-info-sign"></i> 没有结果</div>').appendTo($(this.ui.list));
            if(this.ui.pager){
                $(this.ui.pager).find('.pager-bar').remove();
                return;
            }
        }else{
            //console.log(resultHtml);
            $(resultHtml).appendTo($(this.ui.list));
        }

    }//-/

    /**
     * setPage
     * @param curPage
     */
    setPage(curPage){
        this.view(curPage);
    }//-/




}//==/