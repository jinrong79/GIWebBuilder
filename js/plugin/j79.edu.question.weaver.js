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
        let T=this;
        //title : 考卷标题
        this.curPageTitle=data.title || null;
        //difficultyLevel: 难度等级，0开始
        this.curPageDifficultyLevel=data.difficultyLevel || 0
        //knowledgeCode: 知识点编码
        this.curKnowledgeCode = data.knowledgeCode || null

        let newData;

        //start load settings and generate
        if(!this.questionData && this.questionDataURL){

            $.ajax(
                this.questionDataURL+'?rnd='+Math.random(),
                {
                    "dataType":"json",
                    "success": function(data, txtStatus, jqXHR) {
                        console.log(data)
                        T.questionData = data
                        T.makePage();
                    }
                }


            )

            /*$.getJSON(
                this.questionDataURL+'?rnd='+Math.random(), newData,
                function(result){
                    console.log(result)
                    T.questionData = result
                    T.makePage();
                })*/


            /*this.loadSetting().then(result => {
                console.log(result)
                this.questionData = result
                this.makePage();
            }).catch(e=>{
                console.log('setting file load error!')
            })*/
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

        this.questionContainer = params.questionContainer || '#question_paper';
        this.answerContainer = params.answerContainer || '#answer_paper';
        this.paparTitleContainer = params.paperTitleContainer || '#paper_title'




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
                        console.log(json)
                        resolve(json)
                    })

            } else {
                reject(new Error())
            }
        })
    }//-/
    makePage(){

        let i;

        //get all questions match the condition:
        let qData=this.questionData.data || [];
        let curData= [];
        for(i=0;i<qData.length;i++){
            if(qData[i].code && qData[i].code.toUpperCase().indexOf(this.curKnowledgeCode.toUpperCase()) === 0 && qData[i].level == this.curPageDifficultyLevel){
                curData.push(qData[i])
            }
        }
        //get random questions from this collections for single paper.
        let qIDs=[];
        let selected=[];
        for(i=0;i<curData.length;i++){
            qIDs.push(i)
        }
        if(curData.length > this.questionAmount){

            i=0;
            do{
                if( Math.random() <= this.questionAmount /curData.length ){
                    selected.push(qIDs[i])
                    qIDs.splice(i,1)
                }
                i++;
                if(i>=qIDs.length){
                    i=0
                }

            }while(selected.length < this.questionAmount)
        }else{
            selected=qIDs;
        }

        console.log('selected:')
        console.log(selected);

        //make page.
        let result = this.generatePageHtml(curData,selected);
        if(result.question){
           $(result.question).appendTo(this.questionContainer)
        }
        if(result.answer){
            $(result.answer).appendTo(this.answerContainer)
        }

        $(this.paparTitleContainer).text(this.curPageTitle)







    }//-/

    generatePageHtml(curData, selected){
        let i;
        let amountPerRow= this.questionAmount / this.columnAmount
        let colClass = 'col-'+ (12 / this.columnAmount)
        let html='';
        let htmlAnswer='';
        for(i=0;i<selected.length;i++){
            if( i % amountPerRow == 0){
                html+='<div class="q-col '+colClass+'">'
            }

            html+='<div class="q-block">' +
                '<div class="q-no">'+(i+1)+'</div>' +
                '<div class="q">' +
                '<img src="'+curData[selected[i]].question.url +'" />'+
                '</div>' +
                '</div>';

            htmlAnswer+='<div class="answer-block">' +
                '<div class="answer">' +'<span class="answer-no">'+(i+1)+'.</span> ' +
                curData[selected[i]].answer.content +
                '</div>' +
                '<div class="explanation">' +
                curData[selected[i]].explanation.content +
                '</div>' +
                '</div>';


            if( i % amountPerRow == amountPerRow-1){
                html+='</div>'
            }

        }
        return {
            question: html,
            answer: htmlAnswer
        }
    }//-/

}
