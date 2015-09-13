import java.util.*;
import java.nio.file.*;
import java.nio.charset.*;
import java.io.*;
import uk.ac.cam.ch.wwmm.oscar.Oscar;
import uk.ac.cam.ch.wwmm.oscar.chemnamedict.entities.*;

public class OscarFile
{
	public static void main(String[] args)
	{
		new OscarFile(args[0]);	
	}

	public OscarFile(String path)
	{
		//read string from file
		try{
			Oscar oscar = new Oscar();

			List<String> lines = Files.readAllLines(Paths.get(path), Charset.forName("UTF-8"));		
			for (String line : lines)
			{
				List<ResolvedNamedEntity> entities = oscar.findAndResolveNamedEntities(line);
				for (ResolvedNamedEntity ne : entities)
				{
					//System.out.println(ne.getSurface());	
					ChemicalStructure stdInchi = ne.getFirstChemicalStructure(FormatType.STD_INCHI);
					if(stdInchi != null)
					{
						System.out.println(ne.getSurface());
					}
				}	
			}					
		} catch (IOException e){
			e.printStackTrace();
		}
	}
}
