/**
 * Messages js file.
 *
 * Handles javascript stuff related to messages function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>
		
		function limitChars(textid, limit, infodiv)
		{
			var text = $('#'+textid).val();	
			var textlength = text.length;
			if(textlength > limit)
			{
				$('#' + infodiv).html('You cannot write more then '+limit+' characters!');
				$('#'+textid).val(text.substr(0,limit));
				return false;
			}
			else
			{
				$('#' + infodiv).html('You have '+ (limit - textlength) +' characters left.');
				return true;
			}
		}
		
		function showReply(id)
		{
			if (id) {
				$('#' + id).toggle(400);
			}
		}
		
		function showReplies(id)
		{
			if (id)
			{
				$('#replies_' + id).toggle(400);
			}
		}
		
		function showRead(id)
		{
			if (id)
			{
				$.get("<?php echo url::base() . 'admin/messages/read/' ?>"+id);
				$('#post_' + id).effect("highlight", {}, 2000);
				$('#post_' + id).removeClass("new_reply");
			}
		}
		
		function showLock(id)
		{
			if (id)
			{
				$.post("<?php echo url::base() . 'admin/messages/lock/' ?>"+id, { },
					function(data)
					{
						if (data.status == 'success')
						{
							alert(data.message);
							$('#post_' + id).addClass("new_lock");
							$('#lock_message_' + id).html('<a href="javascript:showUnlock('+id+')">+UNLock</a>');
						}
						else
						{
							alert(data.message);
						}
					}, "json");
			}
		}
		
		function showUnlock(id)
		{
			if (id)
			{
				$.post("<?php echo url::base() . 'admin/messages/unlock/' ?>"+id, { },
					function(data)
					{
						if (data.status == 'success')
						{
							alert(data.message);
							$('#post_' + id).effect("highlight", {}, 2000);
							$('#post_' + id).removeClass("new_lock");
							$('#lock_message_' + id).html('<a href="javascript:showLock('+id+')">+Lock</a>');
							$('#lock_' + id).remove();
						}
						else
						{
							alert(data.message);
						}
					}, "json");
			}
		}
		
		function sendMessage(id, loader)
		{
			$('#' + loader).html('<img src="<?php echo url::base() . "media/img/loading_g.gif"; ?>">');
			$.post("<?php echo url::base() . 'admin/messages/send/' ?>", { to_id: id, message: $("#message_" + id).attr("value") },
				function(data){
					if (data.status == 'sent'){
						$('#reply_' + id).hide();
					} else {
						$('#replyerror_' + id).show();
						$('#replyerror_' + id).html(data.message);
						alert('ERROR!');
					}
					$('#' + loader).html('');
			  	}, "json");
		}
		
		function cannedReply(id, field)
		{
			var autoreply;
			$("#" + field).attr("value", "");
			if (id == 1) {
				autoreply = "Thank you for sending us a message. What is the closest town or city to you?";
			}else if (id == 2) {
				autoreply = "Thank you for sending us a message. Can you send more information?"
			}else if (id == 3) {
				autoreply = "Mesi pou voye nou yon mesaj. Ki vil ki pi pwoch ou?"
			}else if (id == 4) {
				autoreply = "Mesi pou voye nou yon mesaj. Eske ou kapab voye nou plis enfomasyon?"
			};
			$("#" + field).attr("value", autoreply);		
		}

        function submitIds()
        {
            if (confirm("Delete cannot be undone. Are you sure you want to continue?"))
                $('#messagesMain').submit(); 
        }
	
		// Ajax Submission
		function itemAction ( action, confirmAction, item_id, level )
		{
			var statusMessage;
			if( !isChecked( "message" ) && item_id=='' )
			{ 
				alert('Please select at least one report.');
			} else {
				var answer = confirm('Are You Sure You Want To ' + confirmAction + ' items?')
				if (answer){
					// Set Submit Type
					$("#action").attr("value", action);
					
					if (item_id != '') 
					{
						// Submit Form For Single Item
						$("#item_single").attr("value", item_id);
						$("#messagesMain").submit();
					}
					else
					{
						// Set Hidden form item to 000 so that it doesn't return server side error for blank value
						$("#item_single").attr("value", "000");
						$("#level").attr("value", level);
						// Submit Form For Multiple Items
						$('#messagesMain').submit();
/*						
						$("input[name='incident_id[]'][checked]").each(
							function() 
							{
								$("#messageMain").submit();
							}
						);
*/						

					}
				
				} else {
					return false;
				}
			}
		}
		
		// Preview Message
		function preview ( id ){
			if (id) {
				$('#' + id).toggle(400);
			}
		}
		
