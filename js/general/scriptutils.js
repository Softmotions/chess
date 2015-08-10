ScriptUtils = function() {
};

ScriptUtils.loadedScripts = new Array();

ScriptUtils.loadScript = function(scriptId) {
  if (ScriptUtils.loadedScripts[scriptId]) {
    return;
  }

  new Ajax.Request(scriptId, { method: 'get', requestHeaders: { pragma: 'no-cache' }, asynchronous: false });
  ScriptUtils.loadedScripts[scriptId] = true;
};

ScriptUtils.trim = function(string) {
  return string.replace(/(^\s+)|(\s+$)/g, '');
};
