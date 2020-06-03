class dataTransporter {


    constructor(params) {
        this.parseParams(params);
    }//-/


    parseParams(params){

        params=params || {};

        this.url=params.url || null;

        this.dataType=params.dataType || 'json';

        this.contentType=params.contentType || 'application/x-www-form-urlencoded';
        //keyName in localStorage for token.
        this.localStorageTokenName=params.localStorageTokenName || 'token';
        //keyName in requestHeader for token
        this.requestHeaderTokenName=params.requestHeaderTokenName || 'token';

        this.HandleFailed=params.HandleFailed || this.defaultHandleFailed;

        this.resultParser=params.resultParser || this.defaultResultParser;

    }//-/

    /**
     * dataPost
     * same as dataGet, except requestType is POST.
     * @param params
     */
    dataPost(params){
        params=params || {};
        params.requestType="POST";
        this.dataGet(params);
    }


    /**
     * dataGet
     * @param params
     *        .url: url for load.
     *        .requestType : GET or POST, default=GET
     *        .dataType: default=json.
     *        .contentType: default="application/x-www-form-urlencoded"
     *        .isSetRequestHeader: bool. [default]true-- add RequestHeader ; false-- no;
     *        .requestHeaderTokenName: token keyname in RequestHeader, default="token";
     *        .localStorageTokenName : token keyname in localStorage, default="token";
     *        .token: token value, if provide this, then do not read from localStorage.
     *
     *
     *        .data   : data to transport to remote.
     *        .success: handler when success.
     *                  success(resultData)
     *                  -resultData: loaded data in correct type indicated by dataType.
     *        .failed: handler when failed:
     *                  failed(failType, txtStatus, data)
     *                  -failType: 1- parse data error;  2- connect server error
     *                  -txtStatus: http reply text status for failure.
     *                  -data: when parse data error, this data contains raw data.
     * @returns {boolean}
     */
    dataGet(params){

        let SELF=this;
        params=params || {};

        let requestType= params.requestType || 'GET';
        let url=params.url || this.url;
        let dataType=params.dataType || this.dataType;
        let contentType=params.contentType || this.contentType;

        let isSetRequestHeader=params.isSetRequestHeader!==false;


        let localStorageTokenName=params.localStorageTokenName || this.localStorageTokenName;
        let requestHeaderTokenName=params.requestHeaderTokenName || this.requestHeaderTokenName;

        let handleSuccess=params.success || null;

        let handlerFailed=params.failed || null;

        let data=params.data || {};




        if(!url){
            console.log("empty url when getting data from remote");
            return false;
        }

        //save current options:
        this.current={};
        this.current.dataType=dataType;
        this.current.contentType=contentType;
        this.current.type=requestType;
        this.current.data=data;

        let optionData={

            "type":requestType,
            "dataType":dataType,
            "contentType":contentType,
            "crossDomain":true,
            "data":data,

            "success": function(data, txtStatus, jqXHR) {

                console.log(data);
                //console.log(txtStatus);
                //console.log(jqXHR);

                let resultData=SELF.resultParser(data,txtStatus,jqXHR)
                if(resultData!==false){
                    if(typeof handleSuccess == 'function'){
                        handleSuccess(resultData);
                        return true;
                    }
                }else{
                    if(typeof handlerFailed == 'function'){
                        handlerFailed(1, txtStatus,data);
                    }else{
                        SELF.HandleFailed(1,txtStatus,data);
                        console.log("failed parsing data!");
                    }
                    return false;

                }
            },//-/success
            "error":function(xmlHR, txtStatus, errThrown){
                if (typeof handlerFailed == 'function') {
                    handlerFailed(2,txtStatus);
                } else {
                    SELF.HandleFailed(2,txtStatus);
                    console.log('failed connecting server!');
                }
                return false;
            },//-/error

        };


        //set RequestHeader:
        if(isSetRequestHeader){
            let token=params.token || null;
            if(!token){
                token=localStorage.getItem(localStorageTokenName);
            }

            optionData.beforeSend=function (XMLHttpRequest) {

                //XMLHttpRequest.setRequestHeader("access-control-allow-headers", "Authorization, Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, X-Requested-By, If-Modified-Since, X-File-Name, X-File-Type, Cache-Control, Origin");
                //XMLHttpRequest.setRequestHeader("access-control-allow-methods", "GET, POST, OPTIONS, PUT, DELETE");
                //XMLHttpRequest.setRequestHeader("access-control-allow-origin", "*");
                //XMLHttpRequest.setRequestHeader("access-control-expose-headers", "Authorization");
                //XMLHttpRequest.setRequestHeader("Access-Control-Allow-Origin", "*");
                if(token){
                    XMLHttpRequest.setRequestHeader(requestHeaderTokenName, token);
                }
            };



        }

        //ajax load:
        $.ajax(url,optionData);


    }//-/dataGetter

    /**
     * defaultResultParser
     * @param data
     * @param txtStatus
     * @param jqXHR
     * @returns {boolean|*|jQuery.fn.init|jQuery|HTMLElement}
     */
    defaultResultParser(data,txtStatus,jqXHR){

        if(this.current.dataType==='json'){
            let jsonData;

            //try to parse result into json format
            try {

                if(typeof data=='object'){
                    jsonData=data;
                }else{
                    //console.log(data);
                    jsonData = JSON.parse(data);
                    //console.log(jsonData);
                }

            } catch (err) { //result format is not json
                console.log("parse json result error!")
                console.log("Result raw data: ");
                console.log(data);
                return false;
            }
            return jsonData;

        }else if(this.current.dataType==='xml'){
            let xmlResult;
            try {

                xmlResult = $(data);
            } catch (err) { //result format is not json
                console.log("parse xml result error!")
                console.log("Result raw data: ");
                console.log(data);
                return false;

            }
            return xmlResult;
        }


    }//-/

    /**
     * defaultHandleFailed
     * @param failType
     * @param txtStatus
     * @param data
     */
    defaultHandleFailed(failType, txtStatus, data){
        alert("failed loading data!");
    }//-/

}