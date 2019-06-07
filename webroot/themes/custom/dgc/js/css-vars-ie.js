// See documentation for option details
// Toggle commented options to see effects
cssVars({
  // fixNestedCalc: false,
  // onlyVars: true,
  // preserve: true,
  // updateURLs: false,
  // variables: { '--font-family': 'helvetica' },
  // ----------
  // include: 'style,link[rel="stylesheet"],link[href="app.css*"]',
  include: 'style,link[href*="app"]',
  onlyLegacy: true,
  preserve: false,
  onError: function(message, node, xhr, url) {
    console.log('ERROR::');
    console.log(message); // 1
    console.log(node); // 2
    console.log(xhr.status); // 3
    console.log(xhr.statusText); // 4
    console.log(url); // 5
  }
});

