class dataTransporter extends dataTransporterBase{

    constructor() {
        super();
    }

    parseParams(params) {
        super.parseParams(params);
        params=params || {};
        //keyName in localStorage for token.
        this.localStorageTokenName=params.localStorageTokenName || 'token';


        this.urlPrefix="https://apps.kbitc.com:8443";
        this.isSetRequestHeader=typeof params.isSetRequestHeader=="undefined" ? true :params.isSetRequestHeader;
    }

    defaultHandlerSuccess(data){



        if(data!==false){
            if(data.code==0){
                //console.log(data)
                console.log("here")
                console.log(data);
                this.onSuccess(data.data,this.caller);
                return true;
            }else{
                this.onFailed(this.caller,data.code,data.message,data,);

                return false;
            }
        }else{
            this.onFailed(this.caller,10000,'parse result data failed!'); //10000 for parsing error-code.
            return false;
        }
    }

}//==/