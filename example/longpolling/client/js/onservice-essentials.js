


var OnServiceEssentials = (function() {

    function OnServiceEssentials() {

        var self = this;

        self.name = 'essentials';
      
        self.stringToObject = function(sJson){
            if(sJson == null) sJson = "";
            eval('var obj='+'{'+sJson+'}');
            return obj;
        }
        
        self.ajax = function(options){
            var url = options['url'] || null; var success = options['success'] || null; var error = options['error'] || null; var progress = options['progress'] || null; var data = options['data'] || null; var method = options['method'] || 'get'; if(options['async'] === false) var async = false; else var async = true; var xhr = (function(){ try{return new XMLHttpRequest();}catch(e){}try{return new ActiveXObject("Msxml3.XMLHTTP");}catch(e){}try{return new ActiveXObject("Msxml2.XMLHTTP.6.0");}catch(e){}try{return new ActiveXObject("Msxml2.XMLHTTP.3.0");}catch(e){}try{return new ActiveXObject("Msxml2.XMLHTTP");}catch(e){}try{return new ActiveXObject("Microsoft.XMLHTTP");}catch(e){}return null; })(); var jsonToQueryString = function(json) { return '' + Object.keys(json).map(function(key) { return encodeURIComponent(key) + '=' + encodeURIComponent(json[key]); }).join('&'); };if(method.toLowerCase() == 'get' && data != null){ url = url + '?'+jsonToQueryString(data); }; xhr.open(method, url, async); if(xhr.upload){ xhr.upload.onprogress = function (e) { if (e.lengthComputable) { if(progress) progress(e.loaded,e.total); } }; xhr.upload.onloadstart = function (e) { if(progress) progress(0,e.total); }; xhr.upload.onloadend = function (e) { if(progress) progress(e.loaded,e.total); }; xhr.upload.onprogress = function (e) { if (e.lengthComputable) { var ratio = Math.floor((e.loaded / e.total) * 100) + '%'; } }; };xhr.onreadystatechange = function () { if(xhr.readyState > 3 ){ if (xhr.status === 404 || xhr.status == 0) { if(error) error(xhr.responseText); return false; } if(success) success(xhr.responseText); } };var dataForm = new FormData(); for (var key in data) { if (data.hasOwnProperty(key)){ dataForm.append(key,data[key]); } }if(method.toLowerCase() == 'get') xhr.send(); else xhr.send(dataForm);
        }

        self.JSON = {
            parse:function(stringJson){
                try {                
                    return JSON.parse(stringJson);
                }catch (e) {
                    return {};
                }              
            },
            check:function(stringJson){
                try {
                    JSON.parse(stringJson);
                } catch (e) {
                    return false;
                }
                return true;
            }
        }
        

       
        

        self.addEvent  = function(objs,event,callback,mode,par1,par2,par3){

                var eventArray = event.split(' ');
                var nArgs = arguments;

                var addEv = function(element,mode,nArgs){

                    if(mode == undefined)
                        mode = true;

                    var objs = element ;

                    if(objs == undefined)
                        objs = window;

                    nArgs_reindex = [];
                    var index = 0;
                    var index2 = 0;
                    nArgs_reindex[0] = false;

                    for (var i = 0; i <nArgs.length; i++) {
                        if(i > 3){
                            nArgs_reindex[nArgs_reindex.length] = nArgs[i];
                        }
                    }
                  

                    if(objs.addEventListener){

                        for (var i = 0; i < eventArray.length; i++) {
                            var ev = eventArray[i];

                            

                            (function(nArgs_reindex,objs,callback,mode,ev){

                                   

                                objs.addEventListener(ev,function(e){
                                 
                                    nArgs_reindex[0] = e;
                                    if(callback)
                                        return callback.apply(objs,nArgs_reindex);
                                },mode);

                            })(nArgs_reindex,objs,callback,mode,ev);

                        }
                    }else if(objs.attachEvent){
                        
                        for (var i = 0; i < eventArray.length; i++) {
                            var ev = eventArray[i];

                            objs.attachEvent('on'+ev,function(e){
                                nArgs_reindex[0] = e;
                                if(callback)
                                    return callback.call(objs,nArgs_reindex);
                            });
                        }
                    }
                }

                if(typeof objs == 'string'){
                    var objs_all = document.querySelectorAll(objs);

                    for (var i = 0; i < objs_all.length; i++) {
                        var element = objs_all[i];

                        (function(element,mode,nArgs){
                            addEv(element,mode,nArgs);
                        })(element,mode,nArgs);
                    }
                    return ;
                }


                addEv(objs,mode,nArgs);


            };


        return this;    
    }

    return new OnServiceEssentials();

});


OnService.attachModule( OnServiceEssentials);
