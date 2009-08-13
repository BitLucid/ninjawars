/*
var odump = (function(){
  var max, INDENT = "                                   "; // As long as you need :)
  
  function valueToStr(value, depth) {
    switch (typeof value) {
      case "object":   return objectToStr(value, depth + 1);
      case "function": return "function";
      default:         return value;
    }
  }
  
  function objectToStr(object, depth) {
    if (depth > max)
      return false;
    
    var output = "";
    for (var key in object)
      output += "\n" + INDENT.substr(0,2*depth) + key + ": " + valueToStr(object[key], depth);

    return output;  
  };
  
  return function odump(object, depth, _max) {
    max = _max || 2;
    return objectToStr(object, depth || 0);
  };
})();
*/
/*

var MAX_DUMP_DEPTH = 5;

      

function dumpObj(obj, name, indent, depth) {
        if (depth > MAX_DUMP_DEPTH) {
                return indent + name + ": <Maximum Depth Reached>\n";
        }
        if (typeof obj == "object") {
                var child = null;
                var output = indent + name + "\n";
                indent += "\t";
                for (var item in obj)
                {
                    try {
                            child = obj[item];
                    } catch (e) {
                            child = "<Unable to Evaluate>";
                    }
                    if (typeof child == "object") {
                            output += dumpObj(child, item, indent, depth + 1);
                    } else {
                            output += indent + item + ": " + child + "\n";
                    }
                }
                return output;
        } else {
                return obj;
        }
}
*/
