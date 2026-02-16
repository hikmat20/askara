<div id="onlyoffice-editor" style="height:800px"></div>

<script src="http://localhost:8070/web-apps/apps/api/documents/api.js"></script>
<script>
  var config = <?= json_encode($payload) ?>;

  console.log(DocsAPI);

  var docEditor = new DocsAPI.DocEditor("onlyoffice-editor", config);
</script>