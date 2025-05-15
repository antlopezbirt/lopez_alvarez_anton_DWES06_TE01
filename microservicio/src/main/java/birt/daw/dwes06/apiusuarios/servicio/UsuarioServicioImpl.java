package birt.daw.dwes06.apiusuarios.servicio;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import birt.daw.dwes06.apiusuarios.dao.UsuarioDAO;
import birt.daw.dwes06.apiusuarios.entity.Usuario;

@Service
public class UsuarioServicioImpl implements UsuarioServicio {
	
	@Autowired
	private UsuarioDAO usuarioDAO;

	@Override
	public List<Usuario> getAll() {
		List<Usuario> usuarios = usuarioDAO.getAll();
		return usuarios;
	}

	@Override
	public Usuario getById(int idUsuario) {
		Usuario usuario = usuarioDAO.getById(idUsuario);
		return usuario;
	}

	@Override
	public void create(Usuario usuario) {
		usuarioDAO.create(usuario);
	}

	@Override
	public void update(Usuario usuario) {
		usuarioDAO.update(usuario);
	}

	@Override
	public void deleteById(int idUsuario) {
		usuarioDAO.deleteById(idUsuario);
	}

}
