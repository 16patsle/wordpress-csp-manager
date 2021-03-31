(function() {
  document.querySelector('#csp_settings').addEventListener('change', function(e) {
    if(e.target.nodeName === 'INPUT' && e.target.type === 'checkbox') {
      var textarea = e.target.parentNode.parentNode.querySelector('textarea');

      if(textarea) {
        textarea.disabled = !e.target.checked
      }
    }
  })
})()