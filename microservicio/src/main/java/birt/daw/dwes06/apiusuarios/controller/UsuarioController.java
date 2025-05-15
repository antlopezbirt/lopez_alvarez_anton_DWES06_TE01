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

import birt.daw.dwes06.apiusuarios.entity.*;
import birt.daw.dwes06.apiusuarios.servicio.*;
import birt.daw.dwes06.apiusuarios.util.Respuesta;
import jakarta.validation.Valid;

@RestController
@RequestMapping("/user")
public class UsuarioController {
	
	@Autowired
	private UsuarioServicio usuarioServicio;
	
	@GetMapping("/")
	public ResponseEntity<Respuesta> getAll() {
		List<Usuario> usuarios = null;
		
		try {
			
			usuarios = usuarioServicio.getAll();
			if (usuarios.isEmpty()) {
				Respuesta respuesta = new Respuesta("Not Found", 404, "No hay usuarios", null);
				return ResponseEntity.ok()
					.body(respuesta);
			} else {
				Respuesta respuesta = new Respuesta("OK", 200, "Todos los usuarios", usuarios);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
		} catch (Exception e) {
			
			Respuesta respuesta = new Respuesta("Server Error", 500, "Se produjo un error en el servidor al recuperar los usuarios", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	@GetMapping("/{idUsuario}")
	public ResponseEntity<Respuesta> getUsuarioById(@PathVariable int idUsuario) {
		
		Usuario usuario;
		
		try {
			usuario = usuarioServicio.getById(idUsuario);
			
			if (usuario == null) {
				Respuesta respuesta = new Respuesta("Not Found", 404, "No existe el usuario solicitado", null);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
			List<Usuario> usuarios = new ArrayList<Usuario>();
			usuarios.add(usuario);
			
			Respuesta respuesta = new Respuesta("OK", 200, "Usuario solicitado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);
		} catch (Exception e) {
			Respuesta respuesta = new Respuesta("Server Error", 500, "Se produjo un error en el servidor al recuperar el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		} 
	}
	
	@PostMapping("/")
	public ResponseEntity<Respuesta> create(@Valid @RequestBody Usuario usuario) {
		usuario.setId(0);
		
		try {
			usuarioServicio.create(usuario);
			List<Usuario> usuarios = new ArrayList<Usuario>();
			usuarios.add(usuario);
			
			Respuesta respuesta = new Respuesta("No Content", 204, "Usuario creado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);

		} catch(Exception e) {
			
			Respuesta respuesta = new Respuesta("Server Error", 500, "Se produjo un error en el servidor al crear el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	@PutMapping("/")
	public ResponseEntity<Respuesta> update(@Valid @RequestBody Usuario usuario) {
		
		Usuario usuarioEditar = null;

		try {
			usuarioEditar = usuarioServicio.getById(usuario.getId());
			
			if (usuarioEditar == null) {
				Respuesta respuesta = new Respuesta("Not Found", 404, "No existe el usuario a actualizar", null);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
			usuarioServicio.update(usuario);
			List<Usuario> usuarios = new ArrayList<Usuario>();
			usuarios.add(usuario);
			
			Respuesta respuesta = new Respuesta("No Content", 204, "Usuario actualizado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);

		} catch(Exception e) {
			Respuesta respuesta = new Respuesta("Server Error", 500, "Se produjo un error en el servidor al actualizar el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	@DeleteMapping("/{idUsuario}")
	public ResponseEntity<Respuesta> delete(@PathVariable int idUsuario) {
		
		Usuario usuarioEliminar = null;

		try {
			usuarioEliminar = usuarioServicio.getById(idUsuario);
			
			if (usuarioEliminar == null) {
				Respuesta respuesta = new Respuesta("Not Found", 404, "No existe el usuario a eliminar", null);
				return ResponseEntity.ok()
					.body(respuesta);
			}
			
			usuarioServicio.deleteById(idUsuario);
			
			List<Usuario> usuarios = new ArrayList<Usuario>();
			usuarios.add(usuarioEliminar);
			
			Respuesta respuesta = new Respuesta("No Content", 204, "Usuario eliminado", usuarios);
			return ResponseEntity.ok()
				.body(respuesta);

		} catch(Exception e) {
			Respuesta respuesta = new Respuesta("Server Error", 500, "Se produjo un error en el servidor al eliminar el usuario", null);
			return ResponseEntity.ok()
				.body(respuesta);
		}
	}
	
	
	
	// Método para manejar las excepciones de validación de los objetos Usuario recibidos
	
	@ExceptionHandler(MethodArgumentNotValidException.class)
	private ResponseEntity<Respuesta> handleValidationExceptions(
	  MethodArgumentNotValidException ex) {
	    Map<String, String> errors = new HashMap<>();
	    ex.getBindingResult().getAllErrors().forEach((error) -> {
	        String fieldName = ((FieldError) error).getField();
	        String errorMessage = error.getDefaultMessage();
	        errors.put(fieldName, errorMessage);
	    });
	    
	    List<Map<String, String>> errores = new ArrayList<Map<String, String>>();
	    errores.add(errors);
	    
	    Respuesta respuesta = new Respuesta("Bad Request", 400, "Datos de entrada mal formados", errores);
		return ResponseEntity.ok()
			.body(respuesta);
	}

}
