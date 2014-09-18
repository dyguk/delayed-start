{* template block that contains the new field *}
<table>
	<tr id="startdelay-tr">
		<td class="label"><label>Start delay</label></td>
		<td>
			{$form.startdelay.html}
			<br>
			<span class="description">Use to delay the start of the payment by this nummer of days.</span>
		</td>
	</tr>
</table>
{* reposition the above block after tr.crm-membership-type-form-block-auto_renew *}
<script type="text/javascript">
	var startdelay_table = cj('#startdelay-tr').parent();
	cj('#startdelay-tr').insertAfter('tr.crm-membership-type-form-block-auto_renew');
	startdelay_table.remove();
</script>
