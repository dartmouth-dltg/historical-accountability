/**
 *  @file processAttributes.js
 *  @description Remove all attributes from an element.
 *  With thanks to https://stackoverflow.com/questions/1870441/remove-all-attributes
 */


jQuery.fn.removeAttributes = function() {
  return this.each(function() {
    var attributes = $.map(this.attributes, function(item) {
      return item.name;
    });
    var e = $(this);
    $.each(attributes, function(i, item) {
      e.removeAttr(item);
    });
  });
}

jQuery.fn.stashAttributes = function(prefix) {
  
  prefix = prefix != null ? prefix : 'stash';
  
  return this.each(function() {
    var attributes = $.map(this.attributes, function(item) {
      return item.name;
    });
    var e = $(this);
    $.each(attributes, function(i, item) {
      var stash = e.attr(item);
      e.removeAttr(item);
      e.attr('data-' + prefix + '-' + item,stash);
    });
  });
}