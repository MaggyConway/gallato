<div id="request_modal">
	<div class="modal--background"></div>

	<div class="modal">
		<form action="/include/send.php" method="POST" class="modal_form">
			<div class="form_title">Оставить заявку</div>
			<input type="text" name="name" placeholder="Имя" required> 
			<input type="text" name="phone" placeholder="Телефон" required> 
			<input type="hidden" name="event" value="FEEDBACK_FORM" />
			<p class="allow">Даю согласие на обработку <a href="/policy/">персональных данных</a></p>
			<button type="submit" class="btn">ОТПРАВИТЬ</button>
		</form>

		<span class="modal_close"></span>
	</div>
	
</div>