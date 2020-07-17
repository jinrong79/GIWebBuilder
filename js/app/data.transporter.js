class dataTransporter extends dataTransporterBase{

    constructor(params) {
        super(params);
    }//-

    /**
     * parseParams
     * parse params and do some initialization for class attribute.
     * @param params
     */
    parseParams(params) {

        params=params || {};

        super.parseParams(params);


        //keyName in localStorage for token.
        this.localStorageTokenName=params.localStorageTokenName || 'token';

        //urlPrefix, usually domain.
        this.urlPrefix="https://apps.kbitc.com:8443";

        //isSetRequestHeader: true[default]; false
        this.isSetRequestHeader=typeof params.isSetRequestHeader=="undefined" ? true :params.isSetRequestHeader;
    }//-/

    /**
     * defaultHandlerSuccess
     * @param data
     * @returns {boolean}
     */
    defaultHandlerSuccess(data){

        if(data!==false){
            if(data.code==0){
                //console.log(data)
                console.log("dataTransporter:")
                console.log(data);
                this.onSuccess(data);
                return true;
            }else{

                //load result, but failed
                this.onFailed(data.code,data.message,data);
                return false;
            }
        }else{

            this.onFailed(20000,'parse result data failed!'); //10000 for parsing error-code.
            return false;

        }
    }//-/



    /**
     * authenticationCheckWhenError
     * check authentication when error occurred
     * @param txtStatus
     * @param errThrown
     * @param xmlHR
     * @returns {boolean}
     */
    authenticationCheckWhenError(txtStatus, errThrown,xmlHR){


        //auth check:
        if(errThrown.toLowerCase()=='unauthorized'){
            document.location.href=j79App.naviURL.login+"?url="+encodeURI(window.location.href);
            return false;
        }

        return true;  //true- OK, continue; false- failed authentication.
    }

}//==/