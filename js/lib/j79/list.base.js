

class ListBase{
    constructor(params) {
        //parse params and initialize object attribute value.
        this.parseParam(params);

    }//-/

    /**
     * parseParam
     * parse params and initialize object attribute value
     * @param params
     */
    parseParam(params){

        if(typeof(params)=='undefined'){
            params={};
        }




        //ui:
        this.ui=params.ui || null;

        //dataOriginal: original list data, not sorted.
        this.dataOriginal=params.listData || null;

        //data:data to generate current list. (after sorting or filtering)
        this.data=this.dataOriginal;

        //itemAmount: all item amount.
        this.itemAmount=this.data ? this.data.length : 0;


        //page type:
        //          0- set paged by self. 1- set paged by server;
        this.pageType=0;

        //perPage: item amount per page. default=10;
        this.perPage=params.perPage || 10;

        //page: current page number.
        this.page=params.page || 1;

        this.pageTotal=0;

        //codeFormat: current list code format
        this.codeFormat=params.codeFormat || 'HTML';

        //itemGenerator: function to generate single list item.
        this.itemGenerator=params.itemGenerator || this.itemGeneratorDefault;


    }//-/

    /**
     * generate
     * generate list code.
     * @param page
     * @returns {string}
     */
    generate(page){
        page= page || 1;
        //this.page=page;
        let result='';
        console.log("here");
        if(!this.itemGenerator){
            return '';
        }
        if(!this.data ||  Object.prototype.toString.call(this.data) !== '[object Array]'){
            result='<div class="list-NA"></div>';
            return result;
        }else{

            let startIdx=0;
            let listLen=this.data.length;
            let endIdx=listLen-1;

            if(this.pageType==0){



                this.pageTotal=Math.ceil(listLen / this.perPage);
                this.itemAmount=listLen;
                this.page=this.pageTotal>=this.page ? this.page : this.pageTotal;

                endIdx=this.page*this.perPage>listLen ? listLen-1:this.page*this.perPage-1;
                startIdx=(this.page-1)*this.perPage;

            }


            for(let i=startIdx;i<=endIdx;i++){

                result+=this.itemGenerator(this.data[i],i);

            }

        }

        return result;

    }//-/

    /**
     * itemGeneratorDefault
     * default item generator function
     * @param itemData
     * @param listIndex
     * @returns {string}
     */
    itemGeneratorDefault(itemData, listIndex){
        let result='';
        let title=itemData.title || '';
        result='<li>'+title+'</li>';
        return result;
    }//-/

    /**
     * filter
     * filter original data to output current data .
     * @param params
     */
    filter(params){
        //overwrite in subclass.
        this.data=this.dataOriginal;
    }//-/


}//=/ListBase