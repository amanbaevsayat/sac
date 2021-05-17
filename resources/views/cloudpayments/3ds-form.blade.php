<form name="acsForm" action="{{ $AcsUrl }}" method="POST">
    <input type="hidden" name="PaReq" value="{{ $PaReq }}">
    <input type="hidden" name="MD" value="{{ $TransactionId }}">
    <input type="hidden" name="TermUrl" value="{{ $TermUrl }}">
</form>
<script>
    console.log('Блаааа');
    acsForm.submit();
</script>
