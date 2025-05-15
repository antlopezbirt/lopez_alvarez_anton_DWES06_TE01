package birt.daw.dwes06.apiusuarios.dao;

import java.util.List;

import birt.daw.dwes06.apiusuarios.entity.Usuario;

public interface UsuarioDAO {
	
	public List<Usuario> getAll();
	public Usuario getById(int idUsuario);
	public void create(Usuario usuario);
	public void update(Usuario usuario);
	public void deleteById(int idUsuario);

}
