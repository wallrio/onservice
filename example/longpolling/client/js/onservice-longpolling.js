
var OnServiceLongPolling = (function() {

    function OnServiceLongPolling() {

        var self = this;

        self.name = 'longpolling';
    
        self.config = function(parameters,statuscon){

            if(statuscon == undefined) statuscon = 'start';

            var timeStamp = Math.floor(Date.now() / 1000);

            var url = parameters['url'];
            var id = parameters['id'] || timeStamp;
            var connectcallback = parameters['connect'] || null;
            var disconnectcallback = parameters['disconnect'] || null;
            var keepcallback = parameters['keep'] || null;
            var stop = parameters['stop'] || null;
            var sendcallback = parameters['sended'] || null;
            var receivedcallback = parameters['received'] || null;
            var error = parameters['error'] || null;
            var options = parameters['options'] || null;
       
            self.id = id;
            self.url = url;
            self.connectcallback = connectcallback;
            self.disconnectcallback = disconnectcallback;
            self.keepcallback = keepcallback;
            self.stop = stop;
            self.sendcallback = sendcallback;
            self.receivedcallback = receivedcallback;
            self.error = error;
            self.options = options;
            self.status = null;
         
            if(parameters['tryagain'] === true)
                var tryagain = true;
            else
                var tryagain = false;

            self.tryagain = tryagain;
       
        }



       
        self.disconnect = function(){

            (new OnService.essentials).ajax({
                url:self.url,
                method:'get',
                data:{"signal":"disconnect","id":self.id},
                error:function(response){
                    self.error("no-connection");                 
                },
                success:function(response){    

                  

                    var responseObj = (new OnService.essentials).JSON.parse(response);
                    if(responseObj.status == 'success'){
                       self.status = 'disconnect';
                        self.disconnectcallback(responseObj);
                    }else self.error(response);

                }
            });

        }


        self.connect = function(){

            self.status = null;

            (new OnService.essentials).ajax({
                url:self.url,
                method:'get',
                data:{"signal":"connect","id":self.id,'options':JSON.stringify(self.options)},
                error:function(response){
                    self.error("no-connection");                 
                },
                success:function(response){    

                    var responseObj = (new OnService.essentials).JSON.parse(response);
                    if(responseObj.status == 'success'){
                        setTimeout(function(){
                            self.keep();
                        });
                        self.connectcallback(responseObj);
                    }else self.error(response);

                }
            });
        }


        // sinaliza recebimento da mensagem
        self.receivedConfirm = function(messagesData){
              
            (new OnService.essentials).ajax({
                url:self.url,
                method:'get',
                data:{"signal":"receivedconfirm","id":self.id,'data':JSON.stringify(messagesData)},
                error:function(response){
                    self.error("no-connection");                 
                },
                success:function(response){      
                    
                    
                    var responseObj = (new OnService.essentials).JSON.parse(response);
                    if(responseObj.status == 'success'                
                        ){
                        self.keep(responseObj);
                      
                    }else{
                        self.error(response);
                    }

                }
            });
        }


        self.keep = function(){
            (new OnService.essentials).ajax({
                url:self.url,
                method:'get',
                data:{"signal":"keep","id":self.id},
                error:function(response){
                    self.error("no-connection");                 
                },
                success:function(response){      


                    if(self.status == 'disconnect')
                        return false;

                    var responseObj = (new OnService.essentials).JSON.parse(response);
                    if(responseObj.status == 'success'
                       
                        ){
                        self.keep(responseObj);
                        self.keepcallback(responseObj);

                    }else if(responseObj.status=='message'){
                        
                        self.receivedConfirm(responseObj.data);            
                        self.receivedcallback(responseObj);

                    }else{
                        self.error(response);
                    }

                }
            });
        }


        



        self.send = function(to,dataJson){

          
            (new OnService.essentials).ajax({
                url:self.url,
                method:'get',
                data:{"signal":'message','to':to,"id":self.id,"data":JSON.stringify(dataJson)},
                success:function(response){    
                       
                    var responseObj = (new OnService.essentials).JSON.parse(response);
                    if(responseObj.status == 'success'){                    
                        self.sendcallback(responseObj);
                    }else self.error(response);

                }
            });
        }




        return this;    
    }

    return new OnServiceLongPolling();

});


OnService.attachModule(OnServiceLongPolling);
