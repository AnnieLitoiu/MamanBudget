<?php
namespace App\Controller;

use App\Entity\Partie;
use App\Entity\Semaine;
use App\Entity\Evenement;
use App\Entity\Option;
use App\Entity\Utilisateur;
use App\Service\GameEngine;
use Doctrine\ORM\EntityManagerInterface as EM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * API "moteur". Renvoie uniquement du JSON.
 */
#[Route('/api/engine')]
class EngineApiController extends AbstractController
{
    public function __construct(private EM $em, private GameEngine $engine) {}

    /** Démarrer une partie */
    #[Route('/start', name: 'engine_start', methods: ['POST'])]
    public function start(Request $req): JsonResponse
    {
        $d = json_decode($req->getContent(), true) ?? [];
        $user = $this->em->getRepository(Utilisateur::class)->find($d['userId'] ?? null);
        if (!$user) return $this->json(['error'=>'user_not_found'], 404);

        $p = (new Partie())
            ->setUtilisateur($user)
            ->setBudgetCourant($d['budget'] ?? '100.00')
            ->setBonheurCourant((int)($d['bonheur'] ?? 50));

        $this->engine->demarrerPartie($p, (int)($d['nbSemaines'] ?? 4));
        $this->em->persist($p); $this->em->flush();

        return $this->json($this->partie($p), 201);
    }

    /** Semaine courante (+ évent. événement par catégorie) */
    #[Route('/{id}/week', name: 'engine_week', methods: ['GET'])]
    public function week(int $id, Request $req): JsonResponse
    {
        $p = $this->em->getRepository(Partie::class)->find($id);
        if (!$p) return $this->json(['error'=>'game_not_found'], 404);

        $s = $this->em->getRepository(Semaine::class)
            ->findOneBy(['partie'=>$p,'numero'=>$p->getSemaineCourante()]);
        if (!$s) return $this->json(['error'=>'week_not_found'], 404);

        $eventPayload = null;
        if ($p->getEtat()==='EN_COURS') {
            $e = $s->getEvenementCourant();
            if (!$e) {
                $cat = $req->query->get('categorie');
                $criteria = ['semaineApplicable'=>$s->getNumero()];
                if ($cat) $criteria['consequenceType'] = $cat;
                $repo = $this->em->getRepository(Evenement::class);
                $cands = $repo->findBy($criteria);
                if ($cands) {
                    $e = $cands[array_rand($cands)];
                    $s->setEvenementCourant($e);
                    $this->em->flush();
                }
            }
            if ($e) { $eventPayload = $this->event($e); }
        }

        return $this->json(['game'=>$this->partie($p),'week'=>$this->semaine($s),'event'=>$eventPayload]);
    }

    /** Appliquer un choix (optionId) puis clôturer la semaine */
    #[Route('/{id}/apply', name: 'engine_apply', methods: ['POST'])]
    public function apply(int $id, Request $req): JsonResponse
    {
        $p = $this->em->getRepository(Partie::class)->find($id);
        if (!$p || $p->getEtat()!=='EN_COURS') return $this->json(['error'=>'not_found_or_ended'], 404);

        $s = $this->em->getRepository(Semaine::class)
            ->findOneBy(['partie'=>$p,'numero'=>$p->getSemaineCourante()]);
        if (!$s) return $this->json(['error'=>'week_not_found'], 404);

        $d = json_decode($req->getContent(), true) ?? [];
        $opt = $this->em->getRepository(Option::class)->find($d['optionId'] ?? null);
        if (!$opt) return $this->json(['error'=>'option_not_found'], 404);
        $evt = $s->getEvenementCourant();
        if (!$evt || !$evt->getOptions()->contains($opt)) {
            return $this->json(['error'=>'option_not_allowed_for_current_event'], 400);
        }

        $this->engine->appliquerOption($p,$s,$opt);
        $this->engine->cloturerSemaine($p,$s);
        $this->em->flush();

        $next = $this->em->getRepository(Semaine::class)
                ->findOneBy(['partie'=>$p,'numero'=>$p->getSemaineCourante()]);

        return $this->json(['ended'=>$p->getEtat()==='TERMINE','game'=>$this->partie($p),'week'=>$next? $this->semaine($next):null]);
    }

    /** Résumé final */
    #[Route('/{id}/summary', name: 'engine_summary', methods: ['GET'])]
    public function summary(int $id): JsonResponse
    {
        $p = $this->em->getRepository(Partie::class)->find($id);
        if (!$p) return $this->json(['error'=>'game_not_found'], 404);
        return $this->json($this->engine->resumeFinal($p));
    }

    /* ===== helpers JSON ===== */
    private function partie(Partie $p): array {
        return ['id'=>$p->getId(),'etat'=>$p->getEtat(),'semaine'=>$p->getSemaineCourante(),
                'nbSemaines'=>$p->getNbSemaines(),'budget'=>$p->getBudgetCourant(),
                'bonheur'=>$p->getBonheurCourant(),'date'=>$p->getDate()?->format('Y-m-d'),
                'userId'=>$p->getUtilisateur()?->getId()];
    }
    private function semaine(Semaine $s): array {
        return ['id'=>$s->getId(),'numero'=>$s->getNumero(),'budgetRestant'=>$s->getBudgetRestant(),
                'bienEtre'=>$s->getBienEtre(),'bonheurEnfants'=>$s->getBonheurEnfants(),
                'eventId'=>$s->getEvenementCourant()?->getId()];
    }
    private function event(Evenement $e): array {
        $opts=[]; foreach($e->getOptions() as $o){ $opts[]=[
            'id'=>$o->getId(),'label'=>$o->getLibelle(),
            'delta'=>['budget'=>(string)$o->getDeltaBudget(),'bien_etre'=>$o->getDeltaBienEtre(),'bonheur'=>$o->getDeltaBonheur()],
        ]; }
        return ['id'=>$e->getId(),'titre'=>$e->getTitre(),'description'=>$e->getDescription(),
                'type'=>$e->getType(),'semaine'=>$e->getSemaineApplicable(),
                'categorie'=>$e->getConsequenceType(),'options'=>$opts];
    }
}
