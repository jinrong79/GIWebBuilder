class dataTransporter extends dataTransporterBase{

    constructor(params) {
        super(params);
    }//-

    parseParams(params) {
        super.parseParams(params);
        params=params || {};
        //keyName in localStorage for token.
        this.localStorageTokenName=params.localStorageTokenName || 'token';


        this.urlPrefix="https://apps.kbitc.com:8443";
        this.isSetRequestHeader=typeof params.isSetRequestHeader=="undefined" ? true :params.isSetRequestHeader;
    }//-/

    defaultHandlerSuccess(data){

        if(data!==false){
            if(data.code==0){
                //console.log(data)
                console.log("dataTransporter:")
                console.log(data);
                this.onSuccess(data,this.caller);
                return true;
            }else{

                //load result, but failed
                this.onFailed(this.caller,data.code,data.message,data);
                return false;
            }
        }else{

            this.onFailed(this.caller,20000,'parse result data failed!'); //10000 for parsing error-code.
            return false;

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

}//==/