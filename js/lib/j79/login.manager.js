class loginManagerBase{

    constructor(params) {
        this.parseParams(params);
    }//-/

    parseParams(params){
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
        //dataTransporter:
        this.dataTransporter=new dataTransporterBase();

    }//-/

    login(params){

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
       requestData.success=this.handleLoginSuccess;
       requestData.failed=this.handleLoginFailed;
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

    handleLoginSuccess(resultData,loginManager){

        if(resultData && resultData.code==0){//login success.
            localStorage.clear();
            loginManager.setLocalStorage(resultData.data);
            if(typeof loginManager.success=='function'){
                loginManager.success(resultData);
            }
            return true;

        }else{//login failed
            if(!resultData){
                console.log("login return empty data!");
                if(typeof loginManager.failed=='function'){
                    loginManager.failed(10000,'empty result data');
                }
                return false;
            }

            console.log("login failed!");
            if(typeof loginManager.failed=='function'){
                loginManager.failed(resultData.code,resultData.message);
            }
            return false;
        }



    }//-/
    handleLoginFailed(loginManager,failType,txtStatus,data){
        console.log("login connect failed.")
        if(typeof loginManager.failed=='function'){
            loginManager.failed(failType,txtStatus);
        }
    }//-/

    setLocalStorage(data){
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
    }//-/




}//==/