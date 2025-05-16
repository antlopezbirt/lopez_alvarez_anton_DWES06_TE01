package birt.daw.dwes06.apiusuarios.controller;

import java.util.HashMap;
import java.util.List;
import java.util.ArrayList;
import java.util.Map;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.FieldError;
import org.springframework.web.bind.MethodArgumentNotValidException;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.modelmapper.ModelMapper;

import birt.daw.dwes06.apiusuarios.dto.UsuarioDTO;
import birt.daw.dwes06.apiusuarios.entity.*;
import birt.daw.dwes06.apiusuarios.servicio.*;
import birt.daw.dwes06.apiusuarios.util.RespuestaApi;
import jakarta.validation.Valid;

@RestController
@RequestMapping("/user")
public class UsuarioController {
	
//	@Autowired
//	private UsuarioServicio usuarioServicio;
	
	@Autowired
	private ModelMapper modelMapper;
	
	private UsuarioServicio usuarioServicio;

	public UsuarioController(UsuarioServicio usuarioServicio) {
		super();
		this.usuarioServicio = usuarioServicio;
	}
	
	@GetMapping("/")
	public ResponseEntity<RespuestaApi> getAll() {
		List<Usuario> usuarios = null;
		
		try {
			
			// Obtenemos las entidades
			
			usuarios = this.usuarioServicio.getAll();
			
			// Mapeamos el listado de entidades a un listado de DTOs
			
			List<UsuarioDTO> usuariosRespuesta = new ArrayList<UsuarioDTO>();
			
			for(Usuario usuario : usuarios) {
				UsuarioDTO usuarioRespuesta = modelMapper.map(usuario, UsuarioDTO.class);
				usuariosRespuesta.add(usuarioRespuesta);
			}
			
			// Enviamos la respuesta, según el caso.
			
			if (usuarios.isEmpty()) {
				RespuestaApi respuesta = new RespuestaApi("Not Found", 404, "No hay usuarios", null);
				return ResponseEntity.ok()
					.body(respuesta);
			} else {
				RespuestaApi respuesta = new RespuestaApi("OK", 200, "Todos los usuarios", usuariosRespuesta);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
		} catch (Exception e) {
			
			RespuestaApi respuesta = new RespuestaApi("Server Error", 500, "Se produjo un error en el servidor al recuperar los usuarios", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	@GetMapping("/{idUsuario}")
	public ResponseEntity<RespuestaApi> getUsuarioById(@PathVariable int idUsuario) {
		
		Usuario usuario;
		
		try {
			usuario = this.usuarioServicio.getById(idUsuario);
			
			// Mapeamos la entidad a DTO y enviamos la respuesta correspondiente, que siempre tiene formato de lista.
			
			UsuarioDTO usuarioRespuesta = modelMapper.map(usuario, UsuarioDTO.class);
			
			if (usuario == null) {
				RespuestaApi respuesta = new RespuestaApi("Not Found", 404, "No existe el usuario solicitado", null);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
			List<UsuarioDTO> usuarios = new ArrayList<UsuarioDTO>();
			usuarios.add(usuarioRespuesta);
			
			RespuestaApi respuesta = new RespuestaApi("OK", 200, "Usuario solicitado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);
		} catch (Exception e) {
			RespuestaApi respuesta = new RespuestaApi("Server Error", 500, "Se produjo un error en el servidor al recuperar el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		} 
	}
	
	@PostMapping("/")
	public ResponseEntity<RespuestaApi> create(@Valid @RequestBody Usuario usuario) {
		usuario.setId(0);
		
		try {
			usuarioServicio.create(usuario);
			
			// Mapeamos la entidad a DTO y enviamos la respuesta correspondiente, que siempre tiene formato de lista.
			
			UsuarioDTO usuarioRespuesta = modelMapper.map(usuario, UsuarioDTO.class);
			
			List<UsuarioDTO> usuarios = new ArrayList<UsuarioDTO>();
			usuarios.add(usuarioRespuesta);
			
			RespuestaApi respuesta = new RespuestaApi("No Content", 204, "Usuario creado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);

		} catch(Exception e) {
			
			RespuestaApi respuesta = new RespuestaApi("Server Error", 500, "Se produjo un error en el servidor al crear el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	@PutMapping("/")
	public ResponseEntity<RespuestaApi> update(@Valid @RequestBody Usuario usuario) {
		
		Usuario usuarioEditar = null;

		try {
			usuarioEditar = usuarioServicio.getById(usuario.getId());
			
			if (usuarioEditar == null) {
				RespuestaApi respuesta = new RespuestaApi("Not Found", 404, "No existe el usuario a actualizar", null);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
			usuarioServicio.update(usuario);
			
			// Mapeamos la entidad a DTO y enviamos la respuesta correspondiente, que siempre tiene formato de lista.
			
			UsuarioDTO usuarioRespuesta = modelMapper.map(usuario, UsuarioDTO.class);
			
			List<UsuarioDTO> usuarios = new ArrayList<UsuarioDTO>();
			usuarios.add(usuarioRespuesta);
			
			RespuestaApi respuesta = new RespuestaApi("No Content", 204, "Usuario actualizado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);

		} catch(Exception e) {
			RespuestaApi respuesta = new RespuestaApi("Server Error", 500, "Se produjo un error en el servidor al actualizar el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	@DeleteMapping("/{idUsuario}")
	public ResponseEntity<RespuestaApi> delete(@PathVariable int idUsuario) {
		
		Usuario usuarioEliminar = null;

		try {
			usuarioEliminar = usuarioServicio.getById(idUsuario);
			
			if (usuarioEliminar == null) {
				RespuestaApi respuesta = new RespuestaApi("Not Found", 404, "No existe el usuario a eliminar", null);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
			usuarioServicio.deleteById(idUsuario);
			
			// Mapeamos la entidad a DTO y enviamos la respuesta correspondiente, que siempre tiene formato de lista.
			
			UsuarioDTO usuarioRespuesta = modelMapper.map(usuarioEliminar, UsuarioDTO.class);
			
			List<UsuarioDTO> usuarios = new ArrayList<UsuarioDTO>();
			usuarios.add(usuarioRespuesta);
			
			RespuestaApi respuesta = new RespuestaApi("No Content", 204, "Usuario eliminado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);

		} catch(Exception e) {
			RespuestaApi respuesta = new RespuestaApi("Server Error", 500, "Se produjo un error en el servidor al eliminar el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	
	
	// Método para manejar las excepciones de validación de los objetos Usuario recibidos
	
	@ExceptionHandler(MethodArgumentNotValidException.class)
	private ResponseEntity<RespuestaApi> handleValidationExceptions(
	  MethodArgumentNotValidException ex) {
	    Map<String, String> errors = new HashMap<>();
	    ex.getBindingResult().getAllErrors().forEach((error) -> {
	        String fieldName = ((FieldError) error).getField();
	        String errorMessage = error.getDefaultMessage();
	        errors.put(fieldName, errorMessage);
	    });
	    
	    List<Map<String, String>> errores = new ArrayList<Map<String, String>>();
	    errores.add(errors);
	    
	    RespuestaApi respuesta = new RespuestaApi("Bad Request", 400, "Datos de entrada mal formados", errores);
		return ResponseEntity.ok()
			.body(respuesta);
	}

}
