// websocket前端基础类,注意WebSocketMain.swf文件地址

;
(function(host, Event, $, undefined) {
    
    var defConfig = {
      WEB_SOCKET_SWF_LOCATION:"WebSocketMain.swf",
      WEB_SOCKET_DEBUG:true,
      // 服务器地址
      WEB_SOCKET_SERVER:"ws://10.10.170.59:9002/"
    };

    var pros = {
      init:function(cfg){
        var me = this;
        me.ws = null;
        me.cfg = cfg;
        me.status = 'disconnected';
        // me.errorTimes=0;
      },

      // 发起连接
      connect:function(){
        var me = this;
        me.ws = new WebSocket(me.cfg.WEB_SOCKET_SERVER);
        me.ws.onopen = function() {
          me.fireEvent("open_after");
        };
        me.ws.onmessage = function(e) {
          var message = $.parseJSON(e.data);
          me.fireEvent("getMessage_after",message)
        };
        me.ws.onclose = function() {
          // do noting
        };
        me.ws.onerror = function(e) {
          me.fireEvent("cantConnect_after");
        };
      },

      // 客户端往后台发送消息
      send:function(message){
        var me = this;
        me.ws.send(message);
      },

      // 客户端主动断开连接
      close:function(){  
        me.ws.close();
      }
    };
    
    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.WebSocket = Main;
})(bomao, bomao.Event,jQuery);
