class dataTransporter extends dataTransporterBase{

    defaultHandlerSuccess(data){



        if(data!==false){
            if(data.code==0){

                this.onSuccess(data.data,this.caller);
            }else{
                this.onFailed(data.code,data.message,data,this.caller);
            }
        }else{
            this.onFailed(this.caller,10000,'parse result data failed!',this.caller); //10000 for parsing error-code.
        }
    }

}//==/