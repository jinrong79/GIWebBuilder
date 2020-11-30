/**
 * CLASS
 * dataTransporterBase
 * transport data with remote by ajax.
 * error code:
 *           10000 : can not connect remote url.
 *           20000 : communicated with remote and got result, but failed parsing result format.
 *           30000 : authentication failed.
 *           other : connect and get result, but result indicates failed loading data,
 *                   in this case, this error_code is provided by server.
 *
 */
class dataTransporterBase{


    constructor(params) {
        this.parseParams(params);
    }//-/


    parseParams(params){

        params=params || {};

        this.url=params.url || null;

        this.dataType=params.dataType || 'json';

        this.contentType=params.contentType || 'application/json';  //x-www-form-urlencoded

        //keyName in localStorage for token.
        this.localStorageTokenName=params.localStorageTokenName || 'token';


        //this.handlerFailed=params.failed || this.defaultHandlerFailed;
        //this.handlerSuccess=params.handlerSuccess || this.defaultHandlerSuccess;

        //call after data transport is finished and  result= success for current operation, set when call dataTransport
        this.onSuccess=null;
        //call after data transport is finished and  result= failed or error for current operation, set when call dataTransport
        this.onFailed=null;

        //this.resultParser=params.resultParser || this.defaultResultParser;

        //url prefix like domain address.
        this.urlPrefix=params.urlPrefix || '';

        //global set request header option.
        this.isSetRequestHeader=typeof params.isSetRequestHeader=="undefined" ? true :params.isSetRequestHeader;


    }//-/

    /**
     * dataPost
     * same as dataTransport, except requestType is POST.
     * @param params
     */
    dataPost(params){
        params=params || {};
        params.requestType="POST";
        this.dataTransport(params);
    }//-/

    /**
     * dataPut
     * same as dataTransport, except requestType is PUT.
     * @param params
     */
    dataPut(params){
        params=params || {};
        params.requestType="PUT";
        this.dataTransport(params);
    }//-/

    /**
     * dataGet
     * same as dataTransport, except requestType is GET.
     * @param params
     */
    dataGet(params){
        params=params || {};
        params.requestType="GET";
        this.dataTransport(params);
    }//-/

    /**
     * dataDelete
     * same as dataTransport, except requestType is DELETE.
     * @param params
     */
    dataDelete(params){
        params=params || {};
        params.requestType="DELETE";
        this.dataTransport(params);
    }


    /**
     * dataTransport
     * transport data between local and remote.
     * @param params
     *        .url: url for load.
     *        .requestType : GET or POST, default=GET
     *        .dataType: default=json.
     *        .contentType: default="application/x-www-form-urlencoded"
     *        .isSetRequestHeader: bool. [default]true-- add RequestHeader ; false-- no;

     *        .token: token value, if provide this, then do not read from localStorage.
     *
     *
     *        .data   : data to transport to remote.
     *        .success: handler when success.
     *                  success(resultData)
     *                  -resultData: loaded data in correct type indicated by dataType.
     *        .failed: handler when failed:
     *                  failed(failType, xmlHR.statusText, xmlHR)
     *                  -failType: 1- parse data error;  2- connect server error
     *                  -statusText: http reply text status for failure.
     *                  -xmlHR: XMLHttpRequest object or other data.
     *        .caller:  caller of current data request.
     * @returns {boolean}
     */
    dataTransport(params){

        let SELF=this;
        params=params || {};

        let requestType= params.requestType || 'GET';
        let url=params.url || this.url;
        let dataType= params.dataType || this.dataType;
        let contentType=requestType=='GET' ? 'application/x-www-form-urlencoded' : 'application/json';  //  params.contentType || this.contentType;

        let isSetRequestHeader=typeof params.isSetRequestHeader=="undefined" ? this.isSetRequestHeader :params.isSetRequestHeader;

        this.localStorageTokenName=params.localStorageTokenName || this.localStorageTokenName;

        //handlers on load success or failed:
        this.onSuccess=params.success || null;
        this.onFailed=params.failed || null;

        //calling object:
        this.caller=params.caller || this;

        let data=params.data || {};



        if(!url){
            console.log("empty url when getting data from remote");
            return false;
        }

        //add url prefix.
        url=this.urlPrefix+url;



        data=requestType=='GET' ? data : JSON.stringify(data);

        //save current options:
        this.current={};
        this.current.dataType=dataType;
        this.current.contentType=contentType;
        this.current.type=requestType;
        this.current.data=data;

        console.log('final data:')
        console.log(data);



        let requestSetting={

            "type":requestType,
            "dataType":dataType,
            "contentType":contentType,
            "crossDomain":true,
            "data":data,

            "success": function(data, txtStatus, jqXHR) {

                console.log(data);
                //console.log(txtStatus);
                //console.log(jqXHR);

                //var xx=jqXHR.getResponseHeader('Set-Cookie');
                /*console.log("set cookie:")
                console.log(jqXHR.getResponseHeader('Set-Cookie'))*/

                //set raw result data:
                SELF.current.rawResult=data;

                //parse result data:
                let resultData=SELF.resultParser(data,txtStatus,jqXHR);

                //call after-load handler:
                SELF.handlerAfterLoad(resultData);



            },//-/success
            "error":function(xmlHR, txtStatus, errThrown){

                console.log(xmlHR);
                console.log(txtStatus);
                console.log(errThrown);

                //check auth:
                if(!SELF.authenticationCheckWhenError(txtStatus,errThrown,xmlHR)){
                    SELF.onFailed(30000,xmlHR.statusText,xmlHR);
                    return;
                }

                //call back:
                if (typeof SELF.onFailed == 'function') {
                    SELF.onFailed(10000,xmlHR.statusText,xmlHR);
                } else {

                    console.log('failed connecting server!');
                    SELF.defaultHandlerLoadFailed(10000,xmlHR.statusText,xmlHR);
                }

            },//-/error

        };


        //set request header:
        if(isSetRequestHeader){

            //get token:
            let token=params.token || null;
            if(!token){
                token=localStorage.getItem(this.localStorageTokenName);
            }
            //console.log("token add to head:");
            //console.log(token);


            if(token){

                requestSetting.beforeSend=function (XMLHttpRequest) {
                    //XMLHttpRequest.setRequestHeader("access-control-allow-headers", "Authorization, Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, X-Requested-By, If-Modified-Since, X-File-Name, X-File-Type, Cache-Control, Origin");
                    //XMLHttpRequest.setRequestHeader("access-control-allow-methods", "GET, POST, OPTIONS, PUT, DELETE");
                    //XMLHttpRequest.setRequestHeader("access-control-allow-origin", "*");
                    //XMLHttpRequest.setRequestHeader("access-control-expose-headers", "Authorization");
                    //XMLHttpRequest.setRequestHeader("Access-Control-Allow-Origin", "*");
                    XMLHttpRequest.setRequestHeader("Accept", "*/*");
                    XMLHttpRequest.setRequestHeader("Authorization", "Bearer " +token);
                    //XMLHttpRequest.setRequestHeader("Connection", "keep-alive");

                };
            }



        }
        console.log("dataTransporter request setting:");
        console.log(requestSetting);

        //ajax load:
        $.ajax(url,requestSetting);


    }//-/dataTransport


    /**
     * handlerAfterLoad
     * when ajax return result successfully, after doing some parsing for result format, will call this handler to proceed the result data.
     * mainly do  success-check and call certain handler to cope with result.
     * [need over-write in sub-class]
     * @param data  {boolean|object}: result data after parsing. false- parse error; other- result data after parsing.
     * @returns {boolean}
     */
    handlerAfterLoad(data){

        //check success or not
        if(data!==false){//success:
            if(typeof this.onSuccess == 'function'){
                //call onSuccess:
                this.onSuccess(data);
                return true;
            }
        }else{//failed:

            console.log("failed parsing data!");

            //if exists onFailed,  then call it:
            if(typeof this.onFailed == 'function'){

                this.onFailed(20000, "parseResultFormatError", this.current.rawResult);

            }else{//or call defaultHandlerLoadFailed:

                this.defaultHandlerLoadFailed(20000,"parseResultFormatError",this.current.rawResult);

            }
            return false;

        }

    }//-/

    /**
     * resultParser
     * parse data format of result from ajax return.
     * @param data
     * @param txtStatus
     * @param jqXHR
     * @returns {boolean|*|jQuery.fn.init|jQuery|HTMLElement}
     */
    resultParser(data,txtStatus,jqXHR){

        //if json:
        if(this.current.dataType==='json'){
            let jsonData;

            //try to parse result into json format
            try {

                if(typeof data=='object'){
                    jsonData=data;
                }else{
                    jsonData = JSON.parse(data);
                }

            } catch (err) { //result format is not json
                console.log("parse json result error!")
                console.log("Result raw data: ");
                console.log(data);
                return false;
            }
            return jsonData;

        }else if(this.current.dataType==='xml'){//if xml
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
     * defaultHandlerLoadFailed
     * default handler for load failed.
     * @param failCode
     * @param txtStatus
     * @param xmlHR: when connect error, it carry xmlHR data.
     */
    defaultHandlerLoadFailed(failCode, txtStatus, xmlHR){
        alert("failed loading data! code:"+failCode+" | status:"+txtStatus);
    }//-/


    /**
     * authenticationCheckWhenError
     * check authentication when error occurred
     * @param txtStatus : get from jquery return when ajax
     * @param errThrown : get from jquery return when ajax
     * @param xmlHR     : get from jquery return when ajax
     * @returns {boolean}
     */
    authenticationCheckWhenError(txtStatus, errThrown,xmlHR){
        //do some check and redirection when failed in authentication.
        //[over-write in sub-class]
        return true;  //true- OK, continue; false- failed authentication.
    }//-/




}