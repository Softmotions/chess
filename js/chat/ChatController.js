ChatController = function(url, key, container) {
  this.url = url;
  this.key = key;
  this.container = container;
};

ChatController.main = function(url, key, container) {
  window.chatc = new ChatController(url, key, container);

  chatc.init();
};

ChatController.prototype = {

  last: null,
  updater: null,

  init: function() {
    var me = this;
    me.initUpdater();
  },

  initUpdater: function() {
    var me = this;
    me.updater = new PeriodicalExecuter(
        function(pe) {
          if (!window.top.__chessUserLogin) {
            pe.stop();
            return;
          }

          new Ajax.Request(
              me.url,
              {
                method: 'post',
                parameters: {
                  action: 'list',
                  key: me.key,
                  last: me.last
                },
                onSuccess: function(transport) {
                  me.evalListResponse(transport)
                }
              }
              );
        },
        5
        );
  },

  setLast: function(last) {
    this.last = last;
  },

  send: function() {
    var me = this;

    var messageEl = $(me.container + '-message');
    if (!messageEl) {
      return;
    }

    var message = messageEl.value;
    messageEl.value = '';
    if (!message || ScriptUtils.trim(message) == '') {
      return;
    }

    new Ajax.Request(
        me.url,
        {
          method: 'post',
          parameters: {
            action: 'send',
            message: message,
            key: me.key,
            last: me.last
          },
          onSuccess: function(transport) {
            me.evalListResponse(transport)
          }
        }
        );
  },

  evalListResponse: function(transport) {
    var me = this;
    if (!transport.responseJSON) {
//      alert(
//          'Не корректный ответ сервера:\n' +
//          transport.responseText
//          );
      return;
    }

    var messagesBox = $(me.container + '-messages');

    var messages = transport.responseJSON.messages;
    messages.each(
        function(item) {
          if (item && item.id && item.id > me.last) {
            me.last = item.id;

            var messageItem = document.createElement('p');
            messageItem.innerHTML = item.time + ', <b>' + item.user + ':</b>&nbsp;' + item.message;
            messageItem.style.margin = 0;
            messagesBox.insert(messageItem);
          }
        }
        );

    var messagesScroll = $(me.container + '-messages-scroll');
    //    messagesScroll.scrollTo(messagesScroll.scrollMaxX || messagesScroll.scrollWidth,
    //                            messagesScroll.scrollMaxY || messagesScroll.scrollHeight);
    messagesScroll.scrollTop = messagesScroll.scrollHeight;
  }

};
