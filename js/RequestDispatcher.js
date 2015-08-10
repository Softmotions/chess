RequestDispatcher = function() {
};

RequestDispatcher.main = function() {
  window.top.rqd = new RequestDispatcher();
};

RequestDispatcher.prototype.applyGateway = function(response) {
  var index;

  var elemId = response.selectSingleNode('gateway/@container').nodeValue;
  var container = $(elemId);
  if (!container) {
    alert('Ошибка отображения. Обновите, пожалуйста, страницу.');
    return;
  }

  var toload = response.selectNodes('gateway/scripts/toload/@src');
  var preload = response.selectNodes('gateway/scripts/preload');
  var postload = response.selectNodes('gateway/scripts/postload');
  var gcontent = response.selectNodes('gateway/content');

  if (toload) {
    index = -1;
    while (++index < toload.length) {
      var scriptId = toload[ index ].nodeValue;
      ScriptUtils.loadScript(scriptId);
    }
  }

  if (preload) {
    index = -1;
    while (++index < preload.length) {
      Try.these(function() {
        eval(preload[ index ].text || preload[ index ].textContent || '')
      });
    }
  }

  // TODO: fix: (text||textContent)
  if (gcontent.length > 0) {
    container.update(gcontent[ 0 ].text || gcontent[ 0 ].textContent || '');
  }

  if (postload) {
    index = -1;
    while (++index < postload.length) {
      Try.these(function() {
        eval(postload[ index ].text || postload[ index ].textContent || '')
      });
    }
  }
};

RequestDispatcher.prototype.doGlobalRequest = function(url, parameters) {
  new Ajax.Request(
      url,
      parameters
      );
};

RequestDispatcher.prototype.doGateWayRequest = function(actionUrl, name, parameters) {
  if (!parameters) {
    parameters = { };
  }

  if (name) {
    parameters.action = name;
  }

  new Ajax.Request(
      actionUrl,
      {
        method: 'post',
        parameters: parameters
      }
      );
};