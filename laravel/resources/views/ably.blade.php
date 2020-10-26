@extends('layouts.game', ['blockUI' => true] )
	@section('contentGame')

		<section class="content-game">
			<span id="name-user" data-name="{{ Auth::user()->name }}"></span>
			<div id="block-pieces" style="display: none;">
				<img src="{{ url('img/balon.png') }}" alt="" id="piece-ball" class="ispiece goal" style="width: 100%;">
				<img src="{{ url('img/j15.png') }}" alt="" id="piece-hw" data-color="1" class="ispiece" style="width: 100%;" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j25.png') }}" alt="" id="piece-hb" data-color="0" class="ispiece" style="width: 100%;" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j11.png') }}" alt="" id="piece-tw" data-color="1" class="ispiece" style="width: 100%;" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j21.png') }}" alt="" id="piece-tb" data-color="0" class="ispiece" style="width: 100%;" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j14.png') }}" alt="" id="piece-aw" data-color="1" class="ispiece" style="width: 100%;" draggable="true" ondragstart="window.drag(event)">
				<img src="{{ url('img/j24.png') }}" alt="" id="piece-ab" data-color="0" class="ispiece" style="width: 100%;" draggable="true" ondragstart="window.drag(event)">
			</div>
			<div id="app">
				<game-init></game-init>
				{{-- <app-game></app-game> --}}
			</div>
			<section id="app-game" class="" v-show="chat">
				<span class="time-game" id="time-game"></span>
				<section id="board-game" class="table-chess">
					<div class="block-ui" id="blockui-board"></div>
				</section>
				<section class="chat">
					<div v-html="message" class="content-chat" id="chat-body">
					</div>
					<div class="form-group mb-0" v-show="chat">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Escriba mensaje" id="text-chat">
							<div class="input-group-append">
								<button class="btn btn-success btn-sm btn-app" v-on:click="writeChat">
									<i class="fa fa-reply"></i>
								</button>
							</div>
						</div>
					</div>
				</section>
			</section>
		</section>
		<script>
			window.onload = function () {
				gameApp()
			}

		</script>

	@endsection