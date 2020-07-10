

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

        //list item order by DESC:  0(false)- ASC; 1- DESC;
        this.listOrderDESC=params.listOrderDESC ? params.listOrderDESC : 0;


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
     * generate list html string by page.
     * @param page
     * @returns {string}
     */
    generate(page){

        //page var check:
        page= !page || page<=0 ? 1 : page;


        let result='';
        //return empty when itemGenerator is not exist:
        if(!this.itemGenerator){
            return '';
        }

        //check data validation:
        if(!this.data ||  Object.prototype.toString.call(this.data) !== '[object Array]' || this.data.length<=0){
            result='<div class="list-NA"></div>';
            return result;

        }else{//if data is valid, then generate:



            //get array of list data:
            let listData=this.data;

            //if list order is DESC:
            let orderFactor=this.listOrderDESC ? -1 :1;

            //set start idx and end idx,
            // when page is set by server and return only current page items by server:
            let listLen=listData.length;
            let startIdx=(listLen-1) * (1 - orderFactor)/2 ;
            let endIdx  =(listLen-1) * (1 + orderFactor)/2 ;



            //if set page by terminal not by server:
            if(this.pageType==0){

                //set page:
                this.page=page;

                //calculate vars for pager:
                this.pageTotal=Math.ceil(listLen / this.perPage);
                this.itemAmount=listLen;
                this.page=this.pageTotal>=this.page ? this.page : this.pageTotal;

                //get start idx and end idx:
                startIdx=(listLen-1) * (1 - orderFactor)/2 +  (this.page-1)*this.perPage*orderFactor;
                endIdx  =(listLen-1) * (1 - orderFactor)/2 +  (this.page*this.perPage-1)*orderFactor;
                startIdx=j79.clamp(startIdx,1,listLen-1);
                endIdx  =j79.clamp(endIdx,1,listLen-1);


            }

            //loop to generate list html:
            for(let i=startIdx;i*orderFactor<=endIdx*orderFactor;i+=orderFactor){

                result+=this.itemGenerator(listData[i],i);

            }

        }

        return result;

    }//-/

    /**
     * itemGeneratorDefault
     * default item generator function.
     * need overwrite in sub-class.
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