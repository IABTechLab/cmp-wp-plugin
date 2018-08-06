// This file is required to inject defaultConfig, don't delete it
var commandQueue = [];
var cmp = function(command, parameter, callback) {
  commandQueue.push({
    command: command,
    parameter: parameter,
    callback: callback
  });
};
cmp.commandQueue = commandQueue;
cmp.config = defaultConfig;
window.__cmp = cmp;