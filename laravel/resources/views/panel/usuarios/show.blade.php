<div class="table-responsive">
	<table class="table table-striped table-bordered table-condensed">
		<tbody id="aRegistros">
			<tr>
				<td><strong>Fecha de ingreso</strong></td>
				<td>{{ $usuario->created_at->format('d/m/Y, h:m A') }}</td>
			</tr>
			<tr>
				<td><strong>Nombre</strong></td>
				<td>{{ $usuario->name }}</td>
			</tr>
			<tr>
				<td><strong>Email</strong></td>
				<td>{{ $usuario->email }}</td>
			</tr>
			<tr>
				<td><strong>Origen</strong></td>
				<td>{{ $usuario->type_auth }}</td>
			</tr>
			<tr>
				<td><strong>Partidas Totales</strong></td>
				<td>{{ $usuario->game_count }}</td>
			</tr>
			<tr>
				<td><strong>Partidas ganadas</strong></td>
				<td>{{ $usuario->game_win }}</td>
			</tr>
			<tr>
				<td><strong>Partidas empatadas</strong></td>
				<td>{{ $usuario->game_empates }}</td>
			</tr>
			<tr>
				<td><strong>Partidas perdidas</strong></td>
				<td>{{ $usuario->game_lose }}</td>
			</tr>
			<tr>
				<td><strong>Puntaje</strong></td>
				<td>{{ $usuario->rating }}</td>
			</tr>
		</tbody>
	</table>
</div>