/**
 * CLASS
 * dataTransporterBase
 * load data by ajax.
 * error code:
 *           10000 : can not connect url.
 *           20000 : get result, but failed parsing result format.
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

        this.contentType=params.contentType || 'application/x-www-form-urlencoded';
        //keyName in localStorage for token.
        this.localStorageTokenName=params.localStorageTokenName || 'token';


        this.handlerFailed=params.failed || this.defaultHandlerFailed;
        this.handlerSuccess=params.handlerSuccess || this.defaultHandlerSuccess;

        this.resultParser=params.resultParser || this.defaultResultParser;

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
     * dataTransport
     * transport data between local and remote.
     * @param params
     *        .url: url for load.
     *        .requestType : GET or POST, default=GET
     *        .dataType: default=json.
     *        .contentType: default="application/x-www-form-urlencoded"
     *        .isSetRequestHeader: bool. [default]true-- add RequestHeader ; false-- no;
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
    dataTransport(params){

        let SELF=this;
        params=params || {};

        let requestType= params.requestType || 'GET';
        let url=params.url || this.url;
        let dataType=params.dataType || this.dataType;
        let contentType=params.contentType || this.contentType;

        let isSetRequestHeader=typeof params.isSetRequestHeader=="undefined" ? this.isSetRequestHeader :params.isSetRequestHeader;
        let localStorageTokenName=params.localStorageTokenName || this.localStorageTokenName;


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

                //console.log(data);
                //console.log(txtStatus);
                //console.log(jqXHR);

                let resultData=SELF.resultParser(data,txtStatus,jqXHR);
                SELF.handlerSuccess(resultData);



            },//-/success
            "error":function(xmlHR, txtStatus, errThrown){

                console.log(xmlHR);
                console.log(txtStatus);
                console.log(errThrown);

                if (typeof SELF.onFailed == 'function') {
                    SELF.onFailed(10000,xmlHR.statusText,xmlHR);
                } else {
                    SELF.handlerFailed(10000,xmlHR.statusText,xmlHR);
                    console.log('failed connecting server!');


                }

            },//-/error

        };


        /*//set RequestHeader:
        console.log("isSetRequestHeader");
        console.log(isSetRequestHeader);*/
        if(isSetRequestHeader){
            let token=params.token || null;
            if(!token){
                token=localStorage.getItem(localStorageTokenName);
            }
            console.log("token add to head:");
            console.log(token);

            if(token){

                optionData.beforeSend=function (XMLHttpRequest) {

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
        console.log("dataTransporter request data:");
        console.log(optionData);

        //ajax load:
        $.ajax(url,optionData);


    }//-/dataTransport

    /**
     * defaultHandlerSuccess
     * @param data
     * @returns {boolean}
     */
    defaultHandlerSuccess(data){

        if(data!==false){
            if(typeof this.onSuccess == 'function'){

                this.onSuccess(data,this.caller);

                return true;
            }
        }else{
            if(typeof this.onFailed == 'function'){
                this.onFailed(20000, txtStatus,data);
            }else{
                this.handlerFailed(20000,txtStatus,data);
                console.log("failed parsing data!");
            }
            return false;

        }

    }//-/

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
     * defaultHandlerFailed
     * @param failCode
     * @param txtStatus
     * @param xmlHR: when connect error, it carry xmlHR data.
     */
    defaultHandlerFailed(failCode, txtStatus, xmlHR){
        alert("failed loading data!");
    }//-/

}