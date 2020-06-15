class loginManager extends loginManagerBase{

    parseParams(params) {
        super.parseParams(params);

        this.requestUsername=params.requestUsername || 'username';
        this.requestPassword=params.requestPassword || 'password';
        this.requestCaptcha=params.requestCaptcha || 'captcha';

        this.storagePrivilege=params.storagePrivilege || 'privileges';
        this.dataKeyPrivilege=params.dataKeyPrivilege || 'privileges';

        this.url="/api/auth/login";
        this.dataTransporter=new dataTransporter();

        this.dataKeySID=params.dataKeySID || 'sid';

    }//

    handleLoginSuccess(resultData){

        console.log(resultData);
        this.setLocalStorage(resultData);

        //loginManager.setLocalStorage(data);
        if(typeof this.success=='function'){
            console.log("call onSuccess")
            this.success(resultData);
        }
        return true;
        //super.handleLoginSuccess(data);
    }//-/

    /**
     * setLocalStorage
     * @param resultData
     */
    setLocalStorage(resultData){

        super.setLocalStorage(resultData);

        let data=resultData.data;
        //set privilege data into localStorage.
        let privilege=data[this.dataKeyPrivilege];
        if(!privilege){
            console.log("no privileges");
        }else{
            localStorage.setItem(this.storagePrivilege,JSON.stringify(privilege));
        }
    }//-/





}