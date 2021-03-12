class j79EduQuestionWeaver{

    constructor(params) {
        //parse params
        this.parseParam(params)


    }//-/

    /**
     * generate
     * start to weave question papers
     */
    generate(data){
        //title : 考卷标题
        this.curPageTitle=data.title || null;
        //difficultyLevel: 难度等级，0开始
        this.curPageDifficultyLevel=data.difficultyLevel || 0
        //knowledgeCode: 知识点编码
        this.curKnowledgeCode = data.knowledgeCode || null

        //start load settings and generate
        if(!this.questionData && this.questionDataURL){
            this.loadSetting().then(result => {
                this.questionData = result
                this.makePage();
            }).catch(e=>{
                console.log('setting file load error!')
            })
        }else if(this.questionData){
            this.makePage();
        }
    }//-/

    /**
     * parseParam
     * @param PARAM
     */
    parseParam(params){

        let T = this;

        params = params || {}
        this.questionDataURL = params.questionDataURL || null;
        this.questionData = params.questionData || null;

        //question total amount in one page
        this.questionAmount= params.questionAmount || 10;
        //columns amount in one page.
        this.columnAmount=params.columnAmount || 2;




    }//-/
    /**
     * loadSetting
     * @returns {Promise<any>}
     */
    loadSetting(){
        return new Promise((resolve, reject) => {
            if (this.questionDataURL) {
                console.log('url ok')

                $.getJSON(
                    this.questionDataURL+'?rnd='+Math.random(),
                    function(json){
                        // console.log(json)
                        resolve(json)
                    })

            } else {
                reject(new Error())
            }
        })
    }//-/
    makePage(){
        let qData=this.questionData.data || [];
        let curData= [];
        for(let i=0;i<qData.length;i++){
            if(qData[i].code && qData[i].code.toUpperCase() == this.curKnowledgeCode.toUpperCase()){
                curData.push(qData[i])
            }
        }
    }//-/

}