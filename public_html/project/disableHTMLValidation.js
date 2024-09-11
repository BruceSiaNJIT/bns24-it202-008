(function disableHTMLValidation (){
    const forms = document.forms;

    for(const form of forms){
        let inputs = form.querySelectorAll("input");

        for(let inp of inputs){
            const attributesToRemove = ["required", "minlength", "maxlength", "min", "max", "step", "pattern"];

            attributesToRemove.forEach(attr => {
                if(inp.hasAttribute(attr)){
                    inp.removeAttribute(attr);
                    console.log(`Removed ${attr} from element ${inp.name || "[No name]"}`);
                }
            });

            if(!["text", "submit", "reset"].includes(inp.type)){
                inp.type = "text";
                console.log(`Changed type to text from element ${inp.name || "[No name]"}`);
            }
        }
    }

    alert("HTML Validation has been disabled until page reload");
})();