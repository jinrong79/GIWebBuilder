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
     * @param data {boolean||object}
     * @returns {boolean}
     */
    handlerAfterLoad(data){

        if(data!==false){
            if(data && data.code==0){
                //console.log(data)
                console.log("dataTransporter:")
                console.log(data);
                if(typeof this.onSuccess == 'function'){
                    this.onSuccess(data);
                }
                return true;
            }else{

                //load result, but failed
                if(typeof this.onFailed == 'function') {//call onFailed:

                    this.onFailed(data.code, data.message, data);

                }else{//or call defaultHandlerLoadFailed:

                    this.defaultHandlerLoadFailed(data.code,data.message,data);

                }
                return false;
            }
        }else{

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