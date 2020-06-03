class loginManager extends loginManagerBase{

    parseParams(params) {
        super.parseParams(params);

        this.requestUsername=params.requestUsername || 'username';
        this.requestPassword=params.requestPassword || 'password';
        this.requestCaptcha=params.requestCaptcha || 'captcha';

        this.storagePrivilege=params.storagePrivilege || 'privileges';
        this.dataKeyPrivilege=params.dataKeyPrivilege || 'privileges';

        this.url="https://apps.kbitc.com:8443/api/auth/login";
        this.dataTransporter=new dataTransporter();
    }//

    handleLoginSuccess(data,loginManager){

        //console.log(loginManager);
        loginManager.setLocalStorage(data);
        if(typeof loginManager.success=='function'){
            console.log("call onSuccess")
            loginManager.success(data);
        }
        return true;
        //super.handleLoginSuccess(data);
    }//-/

    setLocalStorage(data){
        super.setLocalStorage(data);
        let privilege=data[this.dataKeyPrivilege];
        if(!privilege){
            console.log("no privileges");
        }else{
            localStorage.setItem(this.storagePrivilege,privilege);
        }
    }//-/



}