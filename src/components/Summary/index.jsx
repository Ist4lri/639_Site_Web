import Image from 'next/image';
import flag from '../../assets/drapeau_fr.png';

const Summary = () => {
  return (
    <div className="summary-container">
      <div className="background-image"/>
      <div className="black-background">
      <div className="content">
          <h1>Le 639th régiment : Quelques mots...</h1>
                <h3>Une communauté A l&apos;écoute et présente.</h3>
                    <p>
                        Ici, au régiment, on est environ une soixantaine a se battre pour l&apos;empereur ! Lorsque tu nous rejoindras, tu pourras trouver des personnes présentes
                        pour te guider et répondre à toutes tes questions. Tu trouveras également des frères d&apos;armes, qui te soutiendront en tout lieu, et qui sauront te 
                        guider afin que tu passes une bonne soirée, et surtout, que tu ne sois pas perdu au milieu du champ de bataille. De plus, si jamais tu as le moindre soucis,
                        il y a une incroyable équipe de support qui seront ravis de t&apos;aider, tous dirigé par Cicéron, le chef des ressources humaines. Si tu as le moindre problème
                        avec qui que ce soit, il saura trouver une solution afin que tu puisses continuer à être dans une bonne atmosphère. Tu as également Sieger, le responsable modeur,
                        qui a la charge de gérer le mod personnel de la team, ainsi que ce qui touche a du code. Enfin tu as le grand manitou, Jager, qui est le leader de la team.
                        Il s&apos;occupe de tout ce qui est administratif, et gère le serveur administrativement et financièrement parlant.
                    </p>
                <h3>Un serveur français Milsim</h3>
                    <p>
                        Nous sommes une team Française <Image src={flag} alt="Drapeau Français" style={{ width: '20px', height: 'auto', verticalAlign: 'middle' }}/> pratiquant le Milsim
                        semi-sérieux. Lors de nos opérations, nous avons toujours un temps ou nous pouvons délirer, parler, et échanger un peu, mais durant les opérations, un RP sérieux
                        est demandé.  Cependant, un fort esprit de camaraderie est présent, ce qui permettra de combattre en étant entouré d&apos;amis, et permettra de supporter les ordres
                        blasant des gradés (Ou des comissaires...). Mais tes futurs frères d&apos;armes sauront te montrer on fait pour survivre en s&apos;amusant, et en passant une bonne soirée.
                        Enfin, sache que tu n&apos;es pas obligé de connaitre toutes l&apos;histoire de Warhammer pour pouvoir nous rejoindre : A vrai dire, c&apos;est même mieux si tu ne connais pas grand
                        chose, vus qu&apos;un garde est assez ignorants des desseins qui se trouvent au dessus de lui...
                    </p>
                <h3>Comment nous rejoindre ?</h3>
                    <p>
                        Pour nous rejoindre, rien de plus simple : Il suffit que tu rejoindre notre serveur discord ! Puis, après que tu ais lu notre réglement, tu pourras passer un entretien
                        afin de savoir comment t&apos;acceuillir. Tu seras ensuite formé, même si tu ne connais rien à Arma3 : nous pouvons prendre en charge des néophytes totaux, du moment que vous
                        la volonté de servir l&apos;empereur.
                    </p>
                  <h4>Alors ? prêt à nous rejoindre ?</h4>
                  <h5><a href="https://discord.gg/HUwHpEZBZx" target="_blank" rel="noopener noreferrer">Enrole toi aujourd&apos;hui !</a></h5>
        </div>
      </div>
    </div>
  );
}

export default Summary;