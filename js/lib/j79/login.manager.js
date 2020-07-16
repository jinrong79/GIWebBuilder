class loginManagerBase{

    constructor(params) {
        this.parseParams(params);
    }//-/

    parseParams(params){

        params=params || {};

        //id for form element
        this.formUsername=params.formUsername || 'input_username';
        this.formPassword=params.formPassword || 'input_password';
        this.formCaptcha=params.formCaptcha || 'input_captcha';

        //key-name for HttpRequest
        this.requestUsername=params.requestUsername || 'username';
        this.requestPassword=params.requestPassword || 'password';
        this.requestCaptcha=params.requestCaptcha || 'captcha';

        //key-name for loginData
        this.dataKeyProfile=params.dataKeyProfile || 'profile';
        this.dataKeySID=params.dataKeySID || 'sid';

        //localstorge keyname for sid:
        this.storageSID=params.storageSID || 'token';
        this.storageProfile=params.storageProfile || 'profile';

        this.success=params.success || null;
        this.failed=params.failed || null;



        //httpRequest url
        this.url=params.url || null;
        //url for logout
        this.url_logout=params.url_logout || this.url || null;
        //dataTransporter:
        this.dataTransporter=new dataTransporterBase();

    }//-/

    login(params){

        let SELF=this;

        if(!params || typeof params.username=='undefined'){
            console.log("login data is invalid in key username.")
            return false;
        }

       let data={};
       data[this.requestUsername]=params.username;
       data[this.requestPassword]=params.password || null;
       data[this.requestCaptcha]=params.captcha || null;

       let requestData={};
       requestData.data=data;
       requestData.url=this.url;
       requestData.success=function(data){
           SELF.handleLoginSuccess(data);
       };
       requestData.failed=function(errorCode,txtStatus,xmlHR){
           SELF.handleLoginFailed(errorCode,txtStatus,xmlHR);
       }
       requestData.caller=this;
       requestData.isSetRequestHeader=false;

       //console.log(requestData);

       if(typeof this.dataTransporter=='object'){
           this.dataTransporter.dataPost(requestData);
       }else{
           console.log("data transporter not provided");
           return false;
       }

       return true;

    }//-/

    logout(params){
        if(typeof this.dataTransporter=='object'){

            let handlerSuccess=params.success || null;
            let handlerFailed=params.failed || null;

            let requestData={};

            requestData.url=this.url_logout;
            requestData.success=function(data){
                localStorage.clear();
                handlerSuccess(data);
            };
            requestData.failed=function(errorCode,txtStatus,xmlHR){
                handlerFailed(errorCode,txtStatus,xmlHR);
            }
            requestData.caller=this;
            requestData.isSetRequestHeader=true;

            this.dataTransporter.dataPost(requestData);
        }else{
            console.log("data transporter not provided");
            return false;
        }
    }//-/

    handleLoginSuccess(resultData){

        if(resultData && resultData.code==0){//login success.
            localStorage.clear();

            //save data into localStorage
            this.setLocalStorage(resultData);
            if(typeof loginManager.success=='function'){
                loginManager.success(resultData);
            }
            return true;

        }else{//login failed

            //empty resultData:
            if(!resultData){
                console.log("login return empty data!");
                if(typeof this.failed=='function'){
                    this.failed(10000,'empty result data');
                }
                return false;
            }

            //result exists, but server tells error:
            console.log("server return login failure.");
            if(typeof this.failed=='function'){
                this.failed(1,"server return login failure.",resultData);
            }
            return false;
        }



    }//-/
    handleLoginFailed(failType,txtStatus,data){
        console.log("login connect failed.")
        if(typeof this.failed=='function'){
            this.failed(failType,txtStatus,data);
        }
    }//-/

    /**
     * setLocalStorage
     * @param resultData
     */
    setLocalStorage(resultData){


        let data=resultData.data;
        let sid=data[this.dataKeySID];
        if(!sid){
            console.log("no sid");
        }else{
            console.log(sid);
            localStorage.setItem(this.storageSID,sid);
        }

        let profile=data[this.dataKeyProfile];
        if(!profile){
            console.log("no profile");
        }else{
            localStorage.setItem(this.storageProfile,JSON.stringify(data[this.dataKeyProfile]));
        }
    }






}//==/